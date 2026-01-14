<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class WhatsAppOtpController extends Controller
{
    protected WhatsAppOtpService $otpService;

    public function __construct(WhatsAppOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show WhatsApp OTP login form.
     */
    public function showForm(): Response
    {
        // Check if WhatsApp login is enabled
        if (! config('peduliku.login_methods.whatsapp')) {
            abort(404);
        }

        return Inertia::render('Auth/WhatsAppLogin');
    }

    /**
     * Send OTP to WhatsApp number.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => ['required', 'string', 'regex:/^(08|628)[0-9]{8,11}$/'],
        ], [
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 08123456789',
        ]);

        $phone = $request->whatsapp;

        // Check if user exists with this WhatsApp number
        $user = User::where('whatsapp', $phone)->first();

        if (! $user) {
            return back()->withErrors([
                'whatsapp' => 'Nomor WhatsApp tidak terdaftar dalam sistem.',
            ]);
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'whatsapp' => 'Akun Anda telah dinonaktifkan.',
            ]);
        }

        // Generate OTP
        $otp = $this->otpService->generateOtp();

        // Store OTP in cache (expires in 5 minutes)
        $cacheKey = "whatsapp_otp_{$phone}";
        Cache::put($cacheKey, [
            'otp' => $otp,
            'user_id' => $user->id,
            'attempts' => 0,
        ], now()->addMinutes(5));

        // Send OTP via WhatsApp
        $sent = $this->otpService->sendOtp($phone, $otp);

        if (! $sent) {
            return back()->withErrors([
                'whatsapp' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ]);
        }

        return Inertia::render('Auth/WhatsAppVerify', [
            'whatsapp' => $phone,
            'message' => 'Kode OTP telah dikirim ke WhatsApp Anda.',
        ]);
    }

    /**
     * Verify OTP and login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => ['required', 'string'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $phone = $request->whatsapp;
        $cacheKey = "whatsapp_otp_{$phone}";
        $cached = Cache::get($cacheKey);

        if (! $cached) {
            return back()->withErrors([
                'otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.',
            ]);
        }

        // Check attempts
        if ($cached['attempts'] >= 3) {
            Cache::forget($cacheKey);

            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan. Silakan minta kode baru.',
            ]);
        }

        // Verify OTP
        if ($cached['otp'] !== $request->otp) {
            // Increment attempts
            Cache::put($cacheKey, [
                ...$cached,
                'attempts' => $cached['attempts'] + 1,
            ], now()->addMinutes(5));

            return back()->withErrors([
                'otp' => 'Kode OTP tidak valid.',
            ]);
        }

        // OTP valid - login user
        $user = User::find($cached['user_id']);
        Cache::forget($cacheKey);

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => ['required', 'string'],
        ]);

        $phone = $request->whatsapp;
        $cacheKey = "whatsapp_otp_{$phone}";

        // Check cooldown (prevent spam)
        $resendKey = "whatsapp_otp_resend_{$phone}";
        if (Cache::has($resendKey)) {
            return back()->withErrors([
                'whatsapp' => 'Tunggu 60 detik sebelum meminta kode baru.',
            ]);
        }

        // Get user
        $user = User::where('whatsapp', $phone)->first();
        if (! $user) {
            return back()->withErrors([
                'whatsapp' => 'Nomor WhatsApp tidak terdaftar.',
            ]);
        }

        // Generate new OTP
        $otp = $this->otpService->generateOtp();

        Cache::put($cacheKey, [
            'otp' => $otp,
            'user_id' => $user->id,
            'attempts' => 0,
        ], now()->addMinutes(5));

        // Set resend cooldown
        Cache::put($resendKey, true, now()->addSeconds(60));

        // Send OTP
        $this->otpService->sendOtp($phone, $otp);

        return back()->with('message', 'Kode OTP baru telah dikirim.');
    }
}
