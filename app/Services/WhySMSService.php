<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhySMSService
{
    protected $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
    }

public function sendSMS($mobile, $message)
{
    try {
        $apiToken = '1046|WGTMJFtNKsY2oZheN06qL1cviTrZjGBYX6AX0mSP1823eed6';
        $senderId = 'EasyTech';

        // إزالة علامة + من الرقم
        $cleanPhone = ltrim($mobile, '+');

        \Log::info('Attempting to send SMS via WhySMS', [
            'phone' => $cleanPhone,
            'message' => $message
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://bulk.whysms.com/api/http/sms/send', [
            'api_token' => $apiToken,
            'recipient' => $cleanPhone,
            'sender_id' => $senderId,
            'type' => 'plain',
            'message' => $message
        ]);

        \Log::info('WhySMS Response', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                return 'success';
            }
        }

        return 'error';
    } catch (\Exception $e) {
        \Log::error('WhySMS Error', [
            'error' => $e->getMessage(),
            'phone' => $mobile
        ]);
        return 'error';
    }
}


public function sendOTP($mobile, $otp, $type = 'register')
{
    \Log::info('sendOTP called', [
        'mobile' => $mobile,
        'otp' => $otp,
        'type' => $type
    ]);

    $messages = [
        'register' => "Welcome to Sham AlEzz! Your verification code is: {$otp}\nThis code expires in 5 minutes. Please do not share this code.",
        
        'forget_password' => "Sham AlEzz: Use code {$otp} to reset your password.\nThis code expires in 5 minutes. If you didn't request this, please ignore.",
        
        'login' => "Sham AlEzz: Your login code is: {$otp}\nValid for 5 minutes."
    ];
    
    $message = $messages[$type] ?? $messages['register'];
    
    return $this->sendSMS($mobile, $message);
}

    public function checkBalance()
    {
        return ['status' => false, 'message' => 'WhySMS balance check not supported'];
    }

    protected function getConfig()
    {
        $setting = \DB::table('addon_settings')
            ->where('key_name', 'whysms')
            ->where('settings_type', 'sms_config')
            ->first();

        return $setting ? json_decode($setting->live_values, true) : null;
    }
}
