<?php

namespace Tests\Feature;

use App\Models\PremiumPayment;
use App\Models\PremiumPaymentIntent;
use App\Models\User;
use App\Services\BscUsdtPaymentVerifier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PremiumPaymentSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const TX_HASH =
        '0xaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    private const WALLET =
        '0x1111111111111111111111111111111111111111';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'services.premium_payment.rpc_url',
            'https://bsc-rpc.example.test'
        );

        config()->set(
            'services.premium_payment.wallet_address',
            self::WALLET
        );

        config()->set(
            'services.premium_payment.minimum_amount_atomic',
            '500000000000000000'
        );

        config()->set(
            'services.premium_payment.confirmations',
            12
        );

        config()->set(
            'services.premium_payment.premium_days',
            30
        );

        config()->set(
            'services.premium_payment.intent_expiration_minutes',
            60
        );
    }

    public function test_guest_cannot_submit_a_premium_payment(): void
    {
        Http::fake();

        $response = $this->post(
            route('premium.verifyCrypto'),
            [
                'payment_intent' => fake()->uuid(),
                'transaction_hash' => self::TX_HASH,
            ]
        );

        $response->assertRedirect(route('login'));

        Http::assertNothingSent();
    }

    public function test_sepolia_transaction_can_never_activate_premium(): void
    {
        $user = $this->makeUser();
        $intent = $this->openPaymentIntent($user);

        $this->fakeRpc(
            chainId: '0xaa36a7',
            receipt: $this->validReceipt(
                $intent->expected_amount_atomic
            )
        );

        $response = $this->actingAs($user)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($intent)
        );

        $response->assertSessionHasErrors(
            'transaction_hash'
        );

        $this->assertFalse(
            (bool) $user->fresh()->is_premium
        );

        $this->assertDatabaseCount(
            'premium_payments',
            0
        );
    }

    public function test_failed_bsc_transaction_cannot_activate_premium(): void
    {
        $user = $this->makeUser();
        $intent = $this->openPaymentIntent($user);

        $receipt = $this->validReceipt(
            $intent->expected_amount_atomic
        );

        $receipt['status'] = '0x0';

        $this->fakeRpc(receipt: $receipt);

        $response = $this->actingAs($user)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($intent)
        );

        $response->assertSessionHasErrors(
            'transaction_hash'
        );

        $this->assertFalse(
            (bool) $user->fresh()->is_premium
        );

        $this->assertDatabaseCount(
            'premium_payments',
            0
        );
    }

    public function test_confirmed_exact_bsc_usdt_payment_activates_premium(): void
    {
        $user = $this->makeUser();
        $intent = $this->openPaymentIntent($user);

        $this->fakeRpc(
            receipt: $this->validReceipt(
                $intent->expected_amount_atomic
            )
        );

        $response = $this->actingAs($user)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($intent)
        );

        $response->assertRedirect(
            route('dashboard')
        );

        $user->refresh();
        $intent->refresh();

        $this->assertTrue(
            (bool) $user->is_premium
        );

        $this->assertNotNull(
            $user->premium_until
        );

        $this->assertNotNull(
            $intent->consumed_at
        );

        $this->assertDatabaseHas(
            'premium_payments',
            [
                'user_id' => $user->id,
                'payment_intent_id' => $intent->id,
                'network' => 'bsc-mainnet',
                'chain_id' => 56,
                'asset' => 'USDT',
                'token_contract' =>
                    BscUsdtPaymentVerifier::TOKEN_CONTRACT,
                'tx_hash' => self::TX_HASH,
                'receiver_address' => self::WALLET,
                'amount_atomic' =>
                    $intent->expected_amount_atomic,
                'status' => 'verified',
            ]
        );
    }

    public function test_stolen_transaction_hash_cannot_activate_attackers_account(): void
    {
        $victim = $this->makeUser();
        $attacker = $this->makeUser();

        $victimIntent = $this->openPaymentIntent($victim);
        $attackerIntent = $this->openPaymentIntent($attacker);

        $this->assertNotSame(
            $victimIntent->expected_amount_atomic,
            $attackerIntent->expected_amount_atomic
        );

        $this->fakeRpc(
            receipt: $this->validReceipt(
                $victimIntent->expected_amount_atomic
            )
        );

        $this->actingAs($attacker)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($attackerIntent)
        )->assertSessionHasErrors('transaction_hash');

        $this->assertFalse(
            (bool) $attacker->fresh()->is_premium
        );

        $this->assertDatabaseCount('premium_payments', 0);

        $this->actingAs($victim)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($victimIntent)
        )->assertRedirect(route('dashboard'));

        $this->assertTrue(
            (bool) $victim->fresh()->is_premium
        );

        $this->assertSame(
            $victim->id,
            PremiumPayment::query()->sole()->user_id
        );
    }

    public function test_user_cannot_submit_another_users_payment_intent(): void
    {
        Http::fake();

        $victim = $this->makeUser();
        $attacker = $this->makeUser();
        $victimIntent = $this->openPaymentIntent($victim);

        $this->actingAs($attacker)->post(
            route('premium.verifyCrypto'),
            [
                'payment_intent' => $victimIntent->public_id,
                'transaction_hash' => self::TX_HASH,
            ]
        )->assertSessionHasErrors('payment_intent');

        $this->assertFalse(
            (bool) $attacker->fresh()->is_premium
        );

        Http::assertNothingSent();
    }

    public function test_same_transaction_cannot_be_used_by_a_second_user(): void
    {
        $firstUser = $this->makeUser();
        $secondUser = $this->makeUser();

        $firstIntent = $this->openPaymentIntent($firstUser);
        $secondIntent = $this->openPaymentIntent($secondUser);

        $this->fakeRpc(
            receipt: $this->validReceipt(
                $firstIntent->expected_amount_atomic
            )
        );

        $this->actingAs($firstUser)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($firstIntent)
        )->assertRedirect(route('dashboard'));

        $this->actingAs($secondUser)->post(
            route('premium.verifyCrypto'),
            $this->paymentPayload($secondIntent)
        )->assertSessionHasErrors(
            'transaction_hash'
        );

        $this->assertFalse(
            (bool) $secondUser->fresh()->is_premium
        );

        $this->assertSame(
            1,
            PremiumPayment::query()->count()
        );
    }

    private function makeUser(): User
    {
        return User::factory()->create([
            'gender' => 'male',
            'city' => 'Tehran',
        ]);
    }

    private function openPaymentIntent(
        User $user
    ): PremiumPaymentIntent {
        $this->actingAs($user)
            ->get(route('premium.upgrade'))
            ->assertOk();

        return PremiumPaymentIntent::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->firstOrFail();
    }

    private function paymentPayload(
        PremiumPaymentIntent $intent
    ): array {
        return [
            'payment_intent' => $intent->public_id,
            'transaction_hash' => self::TX_HASH,
        ];
    }

    private function fakeRpc(
        string $chainId = '0x38',
        ?array $receipt = null
    ): void {
        $receipt ??= $this->validReceipt(
            '500000000000000000'
        );

        Http::fake(
            function (
                HttpRequest $request
            ) use (
                $chainId,
                $receipt
            ) {
                $method =
                    $request->data()['method'] ?? null;

                return match ($method) {
                    'eth_chainId' => Http::response([
                        'jsonrpc' => '2.0',
                        'id' => 1,
                        'result' => $chainId,
                    ]),

                    'eth_getTransactionReceipt' =>
                        Http::response([
                            'jsonrpc' => '2.0',
                            'id' => 1,
                            'result' => $receipt,
                        ]),

                    'eth_blockNumber' =>
                        Http::response([
                            'jsonrpc' => '2.0',
                            'id' => 1,
                            'result' => '0x6f',
                        ]),

                    default => Http::response([
                        'jsonrpc' => '2.0',
                        'id' => 1,
                        'error' => [
                            'message' =>
                                'Unexpected RPC method',
                        ],
                    ], 400),
                };
            }
        );
    }

    private function validReceipt(
        string $amountAtomic
    ): array {
        return [
            'transactionHash' => self::TX_HASH,
            'status' => '0x1',
            'blockNumber' => '0x64',

            'blockHash' =>
                '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',

            'logs' => [
                [
                    'address' =>
                        BscUsdtPaymentVerifier::TOKEN_CONTRACT,

                    'topics' => [
                        '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef',

                        '0x0000000000000000000000002222222222222222222222222222222222222222',

                        '0x0000000000000000000000001111111111111111111111111111111111111111',
                    ],

                    'data' => '0x'.str_pad(
                        dechex((int) $amountAtomic),
                        64,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'logIndex' => '0x0',
                ],
            ],
        ];
    }
}
