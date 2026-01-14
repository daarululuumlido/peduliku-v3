<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOtpService
{
    protected string $gateway;
    protected string $apiKey;

    public function __construct()
    {
        $this->gateway = config('peduliku.whatsapp_gateway', 'fonnte');
        $this->apiKey = config('peduliku.whatsapp_api_key', '');
    }

    /**
     * Generate a 6-digit OTP code.
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via WhatsApp.
     */
    public function sendOtp(string $phoneNumber, string $otp): bool
    {
        $message = "🔐 *PeduliKu Login*\n\nKode OTP Anda: *{$otp}*\n\nKode berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapapun.";

        return match ($this->gateway) {
            'fonnte' => $this->sendViaFonnte($phoneNumber, $message),
            'wablas' => $this->sendViaWablas($phoneNumber, $message),
            default => $this->logOnly($phoneNumber, $message),
        };
    }

    /**
     * Send via Fonnte gateway.
     */
    protected function sendViaFonnte(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp OTP sent via Fonnte', [
                    'phone' => $phoneNumber,
                    'status' => $response->json('status'),
                ]);
                return true;
            }

            Log::error('Fonnte API error', [
                'phone' => $phoneNumber,
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte API exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send via Wablas gateway.
     */
    protected function sendViaWablas(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post('https://pati.wablas.com/api/send-message', [
                'phone' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp OTP sent via Wablas', [
                    'phone' => $phoneNumber,
                    'status' => $response->json('status'),
                ]);
                return true;
            }

            Log::error('Wablas API error', [
                'phone' => $phoneNumber,
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Wablas API exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Log only (for development without real gateway).
     */
    protected function logOnly(string $phoneNumber, string $message): bool
    {
        Log::info('WhatsApp OTP (dev mode - not sent)', [
            'phone' => $phoneNumber,
            'message' => $message,
        ]);
        return true;
    }

    /**
     * Format phone number to international format.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xx to 628xx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 if not present
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
