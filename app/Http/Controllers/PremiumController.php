<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
class PremiumController extends Controller
{
    public function index()
    {
        return view('premium.index'); 
    }

public function verifyRealCrypto(Request $request)
{
    $request->validate([
        'transaction_hash' => [
            'required',
            'string',
            'regex:/^0x([A-Fa-f0-9]{64})$/',
            'unique:users,last_crypto_hash'
        ],
    ], [
        'transaction_hash.required' => 'وارد کردن هش تراکنش الزامی است.',
        'transaction_hash.regex' => 'فرمت هش تراکنش نامعتبر است.',
        'transaction_hash.unique' => 'این تراکنش قبلاً استفاده شده است!',
    ]);

    $txHash = $request->input('transaction_hash');
    
    // استفاده از RPC رایگان و معتبر شبکه اصلی بایننس (BSC Mainnet)
    $rpcUrl = "https://bsc-dataseed.binance.org/"; 

    try {
        $response = Http::post($rpcUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionByHash',
            'params' => [$txHash],
            'id' => 1
        ]);

        $txData = $response->json('result');

        if (!$txData) {
            return back()->withErrors(['transaction_hash' => 'تراکنشی با این مشخصات یافت نشد!'])->withInput();
        }

        // آدرس قرارداد رسمی تتر (USDT) روی شبکه اصلی بایننس (BSC)
        $bscUsdtContract = strtolower("0x55d398326f99059fF775485246999027B3197955");
        $txTo = strtolower($txData['to']);

        if ($txTo !== $bscUsdtContract) {
            return back()->withErrors(['transaction_hash' => 'این تراکنش مربوط به پرداخت تتر (USDT) در شبکه BSC نیست!'])->withInput();
        }

        $inputData = $txData['input'];
        
        // متد انتقال استاندارد
        if (!str_starts_with($inputData, '0xa9059cbb')) {
            return back()->withErrors(['transaction_hash' => 'نوع تراکنش معتبر نیست!'])->withInput();
        }

        // استخراج آدرس کیف پول شما (گیرنده)
        $receiverHex = substr($inputData, 34, 40);
        $receiverAddress = '0x' . $receiverHex;

        // آدرس ولت مقصد (کیف پول دریافت‌کننده شما)
        $ourWallet = strtolower("0x0a2F71b27902621f3E45356b96018e2358Ce8f89");

        if (strtolower($receiverAddress) !== $ourWallet) {
            return back()->withErrors(['transaction_hash' => 'این تتر به ولت فروشگاه واریز نشده است!'])->withInput();
        }

        // استخراج مبلغ ارسالی (تتر روی BSC دارای ۱۸ رقم اعشار است)
        $valueHex = substr($inputData, 74, 64);
        // تبدیل مقدار هگزسیمال بزرگ به دسی‌مال بر پایه ۱۸ رقم اعشار
        $valueDecimal = gmp_strval(gmp_init($valueHex, 16));
        $actualValue = floatval($valueDecimal) / pow(10, 18); 

        // بررسی اینکه حداقل نیم دلار (0.5 USDT) واریز شده باشد
        if ($actualValue < 0.5) {
            return back()->withErrors(['transaction_hash' => 'مبلغ واریزی کمتر از نیم دلار (0.5 USDT) است!'])->withInput();
        }

    } catch (\Exception $e) {
        return back()->withErrors(['transaction_hash' => 'خطایی در تایید تراکنش رخ داد: ' . $e->getMessage()])->withInput();
    }

    // ارتقای کاربر در صورت صحت اطلاعات
    $user = Auth::user(); 
    $user->update([ 
        'is_premium' => true,
        'last_crypto_hash' => $txHash
    ]);

    return redirect()->route('dashboard')->with('success', 'پرداخت واقعی نیم دلاری تتر شما با موفقیت تایید و اکانت فعال شد!');
}

public function verifyCrypto(Request $request)
    {
        // ۱. بررسی فرمت اولیه هش (هش‌های اتریوم همیشه با 0x شروع شده و ۶۶ کاراکتر هستند)
        $request->validate([
            'transaction_hash' => [
                'required',
                'string',
                'regex:/^0x([A-Fa-f0-9]{64})$/', // بررسی دقیق الگوی هش‌های اتریوم
                'unique:users,last_crypto_hash'
            ],
        ], [
            'transaction_hash.required' => 'وارد کردن هش تراکنش الزامی است.',
            'transaction_hash.regex' => 'فرمت هش تراکنش نامعتبر است. (باید با 0x شروع شده و شامل حروف و اعداد هگزادسیمال باشد)',
            'transaction_hash.unique' => 'این تراکنش قبلاً در سیستم ثبت شده است!',
        ]);

        $txHash = $request->input('transaction_hash');
        
        // ۲. استعلام مستقیم از بلاکچین (شبکه آزمایشی Sepolia) از طریق یک ارائه‌دهنده عمومی رایگان
        $rpcUrl = "https://ethereum-sepolia-rpc.publicnode.com"; // یا آدرس اختصاصی شما در Infura/QuickNode
        
        try {
            $response = Http::post($rpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionByHash',
                'params' => [$txHash],
                'id' => 1
            ]);

            if ($response->failed()) {
                return back()->withErrors(['transaction_hash' => 'ارتباط با شبکه بلاکچین برقرار نشد. بعداً تلاش کنید.'])->withInput();
            }

            $txData = $response->json('result');

            // اگر تراکنش اصلاً در بلاکچین پیدا نشد
            if (!$txData) {
                return back()->withErrors(['transaction_hash' => 'تراکنشی با این مشخصات در شبکه بلاکچین یافت نشد!'])->withInput();
            }

            // ۳. بررسی ولت مقصد (باید مطمئن شویم پول به ولت ما آمده، نه شخص دیگری)
            $ourWallet = strtolower("0x0a2F71b27902621f3E45356b96018e2358Ce8f89");
            $txTo = strtolower($txData['to']);

            if ($txTo !== $ourWallet) {
                return back()->withErrors(['transaction_hash' => 'این تراکنش به ولت ما ارسال نشده است!'])->withInput();
            }

            // ۴. بررسی مبلغ واریزی (درخواست ما 0.003 اتر بود)
            // بلاکچین مبالغ را به واحد Wei برمی‌گرداند. (هر 1 اتر = 10^18 Wei)
            // 0.003 ETH معادل 3000000000000000 Wei است که به هگزادسیمال می‌شود: 0xaa87bee538000
            $requiredValueHex = "0xaa87bee538000"; 
            $txValueHex = $txData['value'];

            // تبدیل هگزادسیمال به عدد معمولی جهت مقایسه راحت‌تر
            $txValue = hexdec($txValueHex);
            $requiredValue = hexdec($requiredValueHex);

            if ($txValue < $requiredValue) {
                return back()->withErrors(['transaction_hash' => 'مبلغ واریزی کمتر از 0.003 ETH است!'])->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors(['transaction_hash' => 'خطایی در بررسی تراکنش رخ داد: ' . $e->getMessage()])->withInput();
        }

        // ۵. در صورت موفقیت تمام مراحل بالا، اکانت ویژه می‌شود
        $user = Auth::user(); 
        $user->update([ 
            'is_premium' => true,
            'last_crypto_hash' => $txHash
        ]);

        return redirect()->route('dashboard')->with('success', 'تراکنش شما تایید شد و حساب شما با موفقیت به حالت ویژه ارتقا یافت!');
    }
}
