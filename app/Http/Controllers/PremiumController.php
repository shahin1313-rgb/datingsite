<?php

namespace App\Http\Controllers;

use App\Models\PremiumPayment;
use App\Models\PremiumPaymentIntent;
use App\Models\User;
use App\Services\BscUsdtPaymentVerifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PremiumController extends Controller
{
    private const PAYMENT_INTENT_CREATION_ATTEMPTS = 10;

    public function index(
        Request $request,
        BscUsdtPaymentVerifier $verifier
    ) {
        try {
            $intent = $this->activeOrCreatePaymentIntent(
                $request->user(),
                $verifier
            );

            $payment = $verifier->paymentDetails(
                $intent->expected_amount_atomic
            );

            $payment['intent_public_id'] = $intent->public_id;
            $payment['expires_at'] = $intent->expires_at;
        } catch (Throwable $exception) {
            report($exception);

            abort(503, 'تنظیمات پرداخت Premium کامل نیست. با پشتیبانی تماس بگیرید.');
        }

        return view('premium.index', compact('payment'));
    }

    public function verifyCrypto(
        Request $request,
        BscUsdtPaymentVerifier $verifier
    ) {
        $validated = $request->validate([
            'payment_intent' => [
                'required',
                'uuid',
            ],

            'transaction_hash' => [
                'required',
                'string',
                'regex:/^0x[A-Fa-f0-9]{64}$/',
            ],
        ], [
            'payment_intent.required' =>
                'فاکتور پرداخت معتبر نیست. صفحه را تازه‌سازی کنید.',

            'payment_intent.uuid' =>
                'فاکتور پرداخت معتبر نیست. صفحه را تازه‌سازی کنید.',

            'transaction_hash.required' => 'وارد کردن هش تراکنش الزامی است.',

            'transaction_hash.regex' =>
                'هش تراکنش باید با 0x شروع شود و دقیقاً ۶۴ کاراکتر هگز داشته باشد.',
        ]);

        $transactionHash = strtolower($validated['transaction_hash']);

        $intent = PremiumPaymentIntent::query()
            ->where('public_id', $validated['payment_intent'])
            ->where('user_id', $request->user()->getKey())
            ->whereNull('consumed_at')
            ->first();

        if (
            $intent === null ||
            $intent->expires_at->isPast()
        ) {
            return back()
                ->withErrors([
                    'payment_intent' =>
                        'این فاکتور منقضی شده یا متعلق به حساب شما نیست. صفحه پرداخت را تازه‌سازی کنید.',
                ])
                ->withInput();
        }

        if ($this->transactionAlreadyUsed($transactionHash)) {
            return back()
                ->withErrors([
                    'transaction_hash' => 'این تراکنش قبلاً برای Premium استفاده شده است.',
                ])
                ->withInput();
        }

        try {
            $verifiedPayment = $verifier->verify(
                $transactionHash,
                $intent->expected_amount_atomic
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'transaction_hash' =>
                        'تراکنش معتبر نیست، مبلغ آن دقیقاً با فاکتور شما برابر نیست، تأیید کافی ندارد یا پرداخت صحیح USDT روی BSC Mainnet پیدا نشد.',
                ])
                ->withInput();
        }

        try {
            $activated = DB::transaction(
                function () use (
                    $request,
                    $intent,
                    $verifiedPayment
                ): bool {
                    $user = User::query()
                        ->whereKey($request->user()->getKey())
                        ->lockForUpdate()
                        ->firstOrFail();

                    $lockedIntent = PremiumPaymentIntent::query()
                        ->whereKey($intent->getKey())
                        ->lockForUpdate()
                        ->first();

                    if (
                        $lockedIntent === null ||
                        (string) $lockedIntent->user_id !==
                            (string) $user->getKey() ||
                        $lockedIntent->consumed_at !== null ||
                        $lockedIntent->expires_at->isPast()
                    ) {
                        return false;
                    }

                    PremiumPayment::create([
                        'user_id' => $user->getKey(),
                        'payment_intent_id' => $lockedIntent->getKey(),
                        'network' => $verifiedPayment['network'],
                        'chain_id' => $verifiedPayment['chain_id'],
                        'asset' => $verifiedPayment['asset'],
                        'token_contract' => $verifiedPayment['token_contract'],
                        'tx_hash' => $verifiedPayment['tx_hash'],
                        'sender_address' => $verifiedPayment['sender_address'],
                        'receiver_address' => $verifiedPayment['receiver_address'],
                        'amount_atomic' => $verifiedPayment['amount_atomic'],
                        'block_number' => $verifiedPayment['block_number'],
                        'confirmations' => $verifiedPayment['confirmations'],
                        'status' => 'verified',
                        'verified_at' => now(),
                    ]);

                    $lockedIntent->forceFill([
                        'consumed_at' => now(),
                    ])->save();

                    $premiumStartsAt =
                        $user->premium_until && $user->premium_until->isFuture()
                            ? $user->premium_until->copy()
                            : now();

                    $user->forceFill([
                        'is_premium' => true,
                        'premium_until' => $premiumStartsAt->addDays(
                            $verifiedPayment['premium_days']
                        ),
                        'last_crypto_hash' => $verifiedPayment['tx_hash'],
                    ])->save();

                    return true;
                },
                3
            );

            if (! $activated) {
                return back()
                    ->withErrors([
                        'payment_intent' =>
                            'این فاکتور منقضی شده یا قبلاً استفاده شده است. صفحه پرداخت را تازه‌سازی کنید.',
                    ])
                    ->withInput();
            }
        } catch (QueryException $exception) {
            if ($this->transactionAlreadyUsed($transactionHash)) {
                return back()
                    ->withErrors([
                        'transaction_hash' =>
                            'این تراکنش قبلاً برای Premium استفاده شده است.',
                    ])
                    ->withInput();
            }

            report($exception);

            return back()
                ->withErrors([
                    'transaction_hash' => 'ثبت پرداخت انجام نشد. دوباره تلاش کنید.',
                ])
                ->withInput();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'transaction_hash' => 'فعال‌سازی Premium انجام نشد. دوباره تلاش کنید.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                sprintf(
                    'پرداخت واقعی USDT تأیید شد و حساب شما برای %d روز ویژه شد.',
                    $verifiedPayment['premium_days']
                )
            );
    }

    private function activeOrCreatePaymentIntent(
        User $authenticatedUser,
        BscUsdtPaymentVerifier $verifier
    ): PremiumPaymentIntent {
        for (
            $attempt = 0;
            $attempt < self::PAYMENT_INTENT_CREATION_ATTEMPTS;
            $attempt++
        ) {
            try {
                return DB::transaction(
                    function () use (
                        $authenticatedUser,
                        $verifier
                    ): PremiumPaymentIntent {
                        $user = User::query()
                            ->whereKey($authenticatedUser->getKey())
                            ->lockForUpdate()
                            ->firstOrFail();

                        $existingIntent = PremiumPaymentIntent::query()
                            ->where('user_id', $user->getKey())
                            ->whereNull('consumed_at')
                            ->where('expires_at', '>', now())
                            ->latest('id')
                            ->first();

                        if ($existingIntent !== null) {
                            return $existingIntent;
                        }

                        $referenceCode = random_int(
                            1,
                            BscUsdtPaymentVerifier::MAX_PAYMENT_REFERENCE
                        );

                        return PremiumPaymentIntent::create([
                            'public_id' => (string) Str::uuid(),
                            'user_id' => $user->getKey(),
                            'reference_code' => $referenceCode,
                            'expected_amount_atomic' =>
                                $verifier->paymentAmountForReference(
                                    $referenceCode
                                ),
                            'expires_at' => now()->addMinutes(
                                $verifier->intentExpirationMinutes()
                            ),
                        ]);
                    },
                    3
                );
            } catch (QueryException $exception) {
                if (! $this->isUniqueConstraintViolation($exception)) {
                    throw $exception;
                }
            }
        }

        throw new RuntimeException(
            'Could not allocate a unique Premium payment reference.'
        );
    }

    private function isUniqueConstraintViolation(
        QueryException $exception
    ): bool {
        $sqlState = (string) (
            $exception->errorInfo[0]
            ?? $exception->getCode()
        );

        $driverCode = (int) (
            $exception->errorInfo[1] ?? 0
        );

        return $sqlState === '23505' ||
            in_array($driverCode, [1062, 2601, 2627], true) ||
            (
                $sqlState === '23000' &&
                str_contains(
                    strtolower($exception->getMessage()),
                    'unique'
                )
            );
    }

    private function transactionAlreadyUsed(string $transactionHash): bool
    {
        return PremiumPayment::query()
            ->where('tx_hash', $transactionHash)
            ->exists();
    }
}
