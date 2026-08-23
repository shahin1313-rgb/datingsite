<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BscUsdtPaymentVerifier
{
    public const CHAIN_ID = 56;

    public const CHAIN_ID_HEX = '0x38';

    public const TOKEN_CONTRACT =
        '0x55d398326f99059ff775485246999027b3197955';

    private const TOKEN_DECIMALS = 18;

    private const TRANSFER_EVENT_TOPIC =
        '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

    public function paymentDetails(): array
    {
        $settings = $this->settings();

        return [
            'network' => 'BSC Mainnet',
            'asset' => 'USDT',
            'wallet_address' => $settings['wallet_address'],
            'amount' => $this->formatAtomicAmount(
                $settings['minimum_amount_atomic'],
                self::TOKEN_DECIMALS
            ),
            'confirmations' => $settings['confirmations'],
            'premium_days' => $settings['premium_days'],
        ];
    }

    public function verify(string $transactionHash): array
    {
        $transactionHash = strtolower(trim($transactionHash));

        if (! preg_match('/^0x[0-9a-f]{64}$/', $transactionHash)) {
            throw new RuntimeException('Invalid transaction hash.');
        }

        $settings = $this->settings();

        $chainId = strtolower(
            (string) $this->rpc(
                $settings['rpc_url'],
                'eth_chainId'
            )
        );

        if ($chainId !== self::CHAIN_ID_HEX) {
            throw new RuntimeException(
                'The configured RPC is not BSC Mainnet.'
            );
        }

        $receipt = $this->rpc(
            $settings['rpc_url'],
            'eth_getTransactionReceipt',
            [$transactionHash]
        );

        if (! is_array($receipt)) {
            throw new RuntimeException(
                'Transaction receipt was not found.'
            );
        }

        if (
            strtolower(
                (string) ($receipt['transactionHash'] ?? '')
            ) !== $transactionHash ||
            strtolower(
                (string) ($receipt['status'] ?? '')
            ) !== '0x1'
        ) {
            throw new RuntimeException(
                'Transaction failed or is invalid.'
            );
        }

        $blockHash = strtolower(
            (string) ($receipt['blockHash'] ?? '')
        );

        if (! preg_match('/^0x[0-9a-f]{64}$/', $blockHash)) {
            throw new RuntimeException(
                'Transaction is not included in a valid block.'
            );
        }

        $transactionBlock = $this->hexToInt(
            $receipt['blockNumber'] ?? null
        );

        $latestBlock = $this->hexToInt(
            $this->rpc(
                $settings['rpc_url'],
                'eth_blockNumber'
            )
        );

        if ($latestBlock < $transactionBlock) {
            throw new RuntimeException(
                'Invalid block confirmation data.'
            );
        }

        $confirmations =
            $latestBlock - $transactionBlock + 1;

        if ($confirmations < $settings['confirmations']) {
            throw new RuntimeException(
                'Transaction does not have enough confirmations.'
            );
        }

        $transfer = $this->findValidTransfer(
            $receipt,
            $settings
        );

        if ($transfer === null) {
            throw new RuntimeException(
                'A valid USDT transfer to the site wallet was not found.'
            );
        }

        return [
            'network' => 'bsc-mainnet',
            'chain_id' => self::CHAIN_ID,
            'asset' => 'USDT',
            'token_contract' => self::TOKEN_CONTRACT,
            'tx_hash' => $transactionHash,
            'sender_address' => $transfer['sender_address'],
            'receiver_address' => $settings['wallet_address'],
            'amount_atomic' => $this->hexToDecimalString(
                $transfer['amount_hex']
            ),
            'block_number' => $transactionBlock,
            'confirmations' => $confirmations,
            'premium_days' => $settings['premium_days'],
        ];
    }

    private function rpc(
        string $rpcUrl,
        string $method,
        array $params = []
    ): mixed {
        $response = Http::asJson()
            ->acceptJson()
            ->timeout(12)
            ->retry(2, 250)
            ->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => 1,
            ]);

        $response->throw();

        $payload = $response->json();

        if (
            ! is_array($payload) ||
            array_key_exists('error', $payload) ||
            ! array_key_exists('result', $payload)
        ) {
            throw new RuntimeException(
                'The BSC RPC returned an invalid response.'
            );
        }

        return $payload['result'];
    }

    private function findValidTransfer(
        array $receipt,
        array $settings
    ): ?array {
        $requiredHex = $this->decimalToHex(
            $settings['minimum_amount_atomic']
        );

        foreach (($receipt['logs'] ?? []) as $log) {
            if (! is_array($log)) {
                continue;
            }

            $topics = $log['topics'] ?? null;

            if (! is_array($topics) || count($topics) < 3) {
                continue;
            }

            $contract = strtolower(
                (string) ($log['address'] ?? '')
            );

            $eventTopic = strtolower(
                (string) ($topics[0] ?? '')
            );

            $senderTopic = strtolower(
                (string) ($topics[1] ?? '')
            );

            $receiverTopic = strtolower(
                (string) ($topics[2] ?? '')
            );

            $amountHex = strtolower(
                (string) ($log['data'] ?? '')
            );

            if (
                $contract !== self::TOKEN_CONTRACT ||
                $eventTopic !== self::TRANSFER_EVENT_TOPIC ||
                ! preg_match(
                    '/^0x[0-9a-f]{64}$/',
                    $senderTopic
                ) ||
                ! preg_match(
                    '/^0x[0-9a-f]{64}$/',
                    $receiverTopic
                ) ||
                ! preg_match(
                    '/^0x[0-9a-f]{64}$/',
                    $amountHex
                )
            ) {
                continue;
            }

            $receiverAddress =
                '0x'.substr($receiverTopic, -40);

            if (
                $receiverAddress !==
                    $settings['wallet_address'] ||
                ! $this->hexIsGreaterThanOrEqual(
                    $amountHex,
                    $requiredHex
                )
            ) {
                continue;
            }

            return [
                'sender_address' =>
                    '0x'.substr($senderTopic, -40),
                'amount_hex' => $amountHex,
            ];
        }

        return null;
    }

    private function settings(): array
    {
        $settings = config(
            'services.premium_payment',
            []
        );

        $rpcUrl = trim(
            (string) ($settings['rpc_url'] ?? '')
        );

        $walletAddress = strtolower(
            trim(
                (string) (
                    $settings['wallet_address'] ?? ''
                )
            )
        );

        $minimumAmount = ltrim(
            trim(
                (string) (
                    $settings['minimum_amount_atomic'] ?? ''
                )
            ),
            '0'
        );

        $minimumAmount =
            $minimumAmount === '' ? '0' : $minimumAmount;

        $confirmations = (int) (
            $settings['confirmations'] ?? 12
        );

        $premiumDays = (int) (
            $settings['premium_days'] ?? 30
        );

        if (
            ! filter_var(
                $rpcUrl,
                FILTER_VALIDATE_URL
            ) ||
            ! str_starts_with($rpcUrl, 'https://') ||
            ! preg_match(
                '/^0x[0-9a-f]{40}$/',
                $walletAddress
            ) ||
            ! preg_match(
                '/^[1-9][0-9]*$/',
                $minimumAmount
            ) ||
            $confirmations < 1 ||
            $premiumDays < 1
        ) {
            throw new RuntimeException(
                'Premium payment configuration is invalid.'
            );
        }

        return [
            'rpc_url' => $rpcUrl,
            'wallet_address' => $walletAddress,
            'minimum_amount_atomic' => $minimumAmount,
            'confirmations' => $confirmations,
            'premium_days' => $premiumDays,
        ];
    }

    private function hexToInt(mixed $hex): int
    {
        if (
            ! is_string($hex) ||
            ! preg_match(
                '/^0x[0-9a-fA-F]+$/',
                $hex
            )
        ) {
            throw new RuntimeException(
                'Invalid hexadecimal block value.'
            );
        }

        $value = hexdec(substr($hex, 2));

        if (
            ! is_int($value) &&
            (
                ! is_float($value) ||
                $value > PHP_INT_MAX
            )
        ) {
            throw new RuntimeException(
                'Block value is too large.'
            );
        }

        return (int) $value;
    }

    private function decimalToHex(
        string $decimal
    ): string {
        $decimal = ltrim($decimal, '0');

        if ($decimal === '') {
            return '0';
        }

        $hex = '';

        while ($decimal !== '0') {
            $quotient = '';
            $remainder = 0;

            foreach (str_split($decimal) as $digit) {
                $number =
                    ($remainder * 10) + (int) $digit;

                $nextDigit = intdiv($number, 16);

                if (
                    $quotient !== '' ||
                    $nextDigit !== 0
                ) {
                    $quotient .= (string) $nextDigit;
                }

                $remainder = $number % 16;
            }

            $hex = dechex($remainder).$hex;

            $decimal =
                $quotient === '' ? '0' : $quotient;
        }

        return $hex;
    }

    private function hexToDecimalString(
        string $hex
    ): string {
        $hex = ltrim(
            strtolower(
                preg_replace('/^0x/', '', $hex)
            ),
            '0'
        );

        if ($hex === '') {
            return '0';
        }

        $decimal = '0';

        foreach (str_split($hex) as $digit) {
            $carry = hexdec($digit);
            $result = '';

            for (
                $index = strlen($decimal) - 1;
                $index >= 0;
                $index--
            ) {
                $value =
                    ((int) $decimal[$index] * 16)
                    + $carry;

                $result =
                    ($value % 10).$result;

                $carry = intdiv($value, 10);
            }

            while ($carry > 0) {
                $result =
                    ($carry % 10).$result;

                $carry = intdiv($carry, 10);
            }

            $decimal =
                ltrim($result, '0') ?: '0';
        }

        return $decimal;
    }

    private function hexIsGreaterThanOrEqual(
        string $actualHex,
        string $requiredHex
    ): bool {
        $actual = ltrim(
            strtolower(
                preg_replace('/^0x/', '', $actualHex)
            ),
            '0'
        ) ?: '0';

        $required = ltrim(
            strtolower(
                preg_replace('/^0x/', '', $requiredHex)
            ),
            '0'
        ) ?: '0';

        if (strlen($actual) !== strlen($required)) {
            return strlen($actual) > strlen($required);
        }

        return strcmp($actual, $required) >= 0;
    }

    private function formatAtomicAmount(
        string $atomic,
        int $decimals
    ): string {
        $atomic = str_pad(
            $atomic,
            $decimals + 1,
            '0',
            STR_PAD_LEFT
        );

        $whole = substr(
            $atomic,
            0,
            -$decimals
        );

        $fraction = rtrim(
            substr($atomic, -$decimals),
            '0'
        );

        return $fraction === ''
            ? $whole
            : $whole.'.'.$fraction;
    }
}