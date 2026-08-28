<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\PremiumPaymentIntent;
use App\Models\User;
use App\Services\BscUsdtPaymentVerifier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class PremiumMessageUnlockTest extends TestCase
{
    use DatabaseTransactions;

    private const TX_HASH =
        '0xaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    public function test_purchase_unlocks_received_private_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $privateMessageText = 'پیام خصوصی پس از خرید نمایش داده می‌شود.';

        $privateMessage = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $privateMessageText,
            'status' => 'private',
        ]);

        $this->actingAs($receiver)
            ->get(route('messages.show', $sender))
            ->assertOk()
            ->assertDontSeeText($privateMessageText)
            ->assertSeeText('برای مشاهده این پیام، باید اکانت');

        $intent = PremiumPaymentIntent::create([
            'public_id' => (string) Str::uuid(),
            'user_id' => $receiver->id,
            'reference_code' => 123456789,
            'expected_amount_atomic' => '501234567890000000',
            'expires_at' => now()->addHour(),
        ]);

        $this->mock(
            BscUsdtPaymentVerifier::class,
            function (MockInterface $mock) use ($intent): void {
                $mock->shouldReceive('verify')
                    ->once()
                    ->with(
                        self::TX_HASH,
                        $intent->expected_amount_atomic
                    )
                    ->andReturn([
                        'network' => 'bsc-mainnet',
                        'chain_id' => 56,
                        'asset' => 'USDT',
                        'token_contract' =>
                            BscUsdtPaymentVerifier::TOKEN_CONTRACT,
                        'tx_hash' => self::TX_HASH,
                        'sender_address' =>
                            '0x2222222222222222222222222222222222222222',
                        'receiver_address' =>
                            '0x1111111111111111111111111111111111111111',
                        'amount_atomic' =>
                            $intent->expected_amount_atomic,
                        'block_number' => 100,
                        'confirmations' => 12,
                        'premium_days' => 30,
                    ]);
            }
        );

        $this->actingAs($receiver)
            ->post(route('premium.verifyCrypto'), [
                'payment_intent' => $intent->public_id,
                'transaction_hash' => self::TX_HASH,
            ])
            ->assertRedirect(route('dashboard'));

        $receiver->refresh();

        $this->actingAs($receiver)
            ->get(route('messages.show', $sender))
            ->assertOk()
            ->assertSeeText($privateMessageText)
            ->assertDontSeeText('برای مشاهده این پیام، باید اکانت');

        $this->assertTrue($receiver->isPremium());
        $this->assertSame('private', $privateMessage->fresh()->status);
    }
}
