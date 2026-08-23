<?php

namespace App\Http\Controllers;

use App\Models\PremiumPayment;
use App\Models\User;
use App\Services\BscUsdtPaymentVerifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PremiumController extends Controller
{
    public function index(BscUsdtPaymentVerifier $verifier)
    {
        try {
            $payment = $verifier->paymentDetails();
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
            'transaction_hash' => [
                'required',
                'string',
                'regex:/^0x[A-Fa-f0-9]{64}$/',
            ],
        ], [
            'transaction_hash.required' => 'وارد کردن هش تراکنش الزامی است.',
            'transaction_hash.regex' =>
                'هش تراکنش باید با 0x شروع شود و دقیقاً ۶۴ کاراکتر هگز داشته باشد.',
        ]);

        $transactionHash = strtolower($validated['transaction_hash']);

        if ($this->transactionAlreadyUsed($transactionHash)) {
            return back()
                ->withErrors([
                    'transaction_hash' => 'این تراکنش قبلاً برای Premium استفاده شده است.',
                ])
                ->withInput();
        }

        try {
            $verifiedPayment = $verifier->verify($transactionHash);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'transaction_hash' =>
                        'تراکنش معتبر نیست، هنوز تأیید کافی ندارد یا پرداخت صحیح USDT روی BSC Mainnet پیدا نشد.',
                ])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $verifiedPayment): void {
                $user = User::query()
                    ->whereKey($request->user()->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                PremiumPayment::create([
                    'user_id' => $user->getKey(),
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
            }, 3);
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

    private function transactionAlreadyUsed(string $transactionHash): bool
    {
        return PremiumPayment::query()
            ->where('tx_hash', $transactionHash)
            ->exists();
    }
}