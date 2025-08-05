<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SmsPasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmsPasswordResetController extends Controller
{
    protected SmsPasswordResetService $smsPasswordResetService;

    public function __construct(SmsPasswordResetService $smsPasswordResetService)
    {
        $this->smsPasswordResetService = $smsPasswordResetService;
    }

    /**
     * Show the form to request password reset via SMS
     */
    public function showRequestForm()
    {
        return view('auth.passwords.sms-request');
    }

    /**
     * Send password reset SMS
     */
    public function sendResetSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->smsPasswordResetService->sendPasswordResetSms($request->phone);

        if ($result['success']) {
            return redirect()->route('password.sms.reset.form')
                ->with('success', $result['message'])
                ->with('phone', $request->phone);
        }

        return back()->withErrors(['phone' => $result['message']])->withInput();
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request)
    {
        $phone = $request->session()->get('phone') ?? $request->get('phone');
        return view('auth.passwords.sms-reset', ['phone' => $phone]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
            'verification_code' => 'required|string|size:6',
            'temporary_password' => 'required|string|min:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->smsPasswordResetService->resetPassword(
            $request->phone,
            $request->verification_code,
            $request->temporary_password,
            $request->password
        );

        if ($result['success']) {
            return redirect()->route('login')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['verification_code' => $result['message']])->withInput();
    }
}
