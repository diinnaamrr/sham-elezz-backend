<?php

namespace App\Http\Controllers;

use App\Services\SMSMisrService;
use App\Services\WhySMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SMSController extends Controller
{
    protected $smsService;

    public function __construct(WhySMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    // Send SMS
    public function sendSMS(Request $request)
    {


    // Validate request
    $request->validate([
        'mobile' => 'required|string',
        'message' => 'required|string',
    ]);
      dd(json_decode(DB::table('addon_settings')
    ->where('key_name', 'whysms')
    ->where('settings_type', 'sms_config')
    ->first()->live_values, true));


    // Log after validation

    // Call SMS service
    $response = $this->smsService->sendSMS($request->mobile, $request->message);

    // Log the response from the service
   

    return response()->json($response);
    }

    // Send OTP
    public function sendOTP(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp' => 'required|string|max:10',
            'template' => 'required|string',
        ]);
       

        $response = $this->smsService->sendOTP($request->mobile, $request->otp, $request->template);

        return response()->json($response);
    }

    // Check Balance
    public function checkBalance()
    {
        $response = $this->smsService->checkBalance();

        return response()->json($response);
    }
  

}
