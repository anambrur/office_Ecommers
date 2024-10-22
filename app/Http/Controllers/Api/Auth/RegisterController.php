<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use Validator;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'string|max:255',
            'mobile' => 'required',
            'email' => 'string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        try {
            $user = User::create([
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => 2,

                'address' => $request->address ?? null,
                'country' => $request->country ?? null,
                'city' => $request->city ?? null,
                'zip' => $request->zip ?? null,

                'billing_firstname' => $request->billing_firstname ?? null,
                'billing_lastname' => $request->billing_lastname ?? null,
                'billing_mobile' => $request->billing_mobile ?? null,
                'billing_address' => $request->billing_address ?? null,
                'billing_city' => $request->billing_city ?? null,
                'billing_zip' => $request->billing_zip ?? null,
            ]);

            $otp = rand(1111, 9999);
            $user->otp = $otp;
            $user->otp_expiry = now()->addMinutes(5);
            $user->save();


            $message = "Your One-Time Login OTP for E-Commerce is " . $otp . ". It will expire in 5 minutes.";
            $this->sendOtp($user->mobile, $message);

            $token = JWTAuth::fromUser($user);

            return response()->json(['success' => 'OTP sent successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while registering the user: ' . $e->getMessage()], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required',
        ]);


        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $otp = rand(1111, 9999);

        $otpExpiry = now()->addMinutes(5);

        $user->otp = $otp;
        $user->otp_expiry = $otpExpiry;
        $user->save();

        $message = "Your password reset OTP for E-Commerce is " . $otp . ". It will expire in 5 minutes.";
        $this->sendOtp($user->mobile, $message);

        return response()->json(['message' => 'OTP sent successfully to your mobile number'], 200);
    }

    public function verifyOtp(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required',
            'otp' => 'required|digits:4',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->otp !== $request->otp || now()->greaterThan($user->otp_expiry)) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        return response()->json(['message' => 'OTP verified successfully'], 200);
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required',
            'otp' => 'required|digits:4',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->otp !== $request->otp || now()->greaterThan($user->otp_expiry)) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $user->password = bcrypt($request->password);
        $user->otp = null;
        $user->otp_expiry = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    private function sendOtp($to, $message)
    {

        $url = "http://139.99.39.237/api/smsapi?api_key=mPs3CslIA2tXhyrIqMip&type=text&number=$to&senderid=8809617613457&message=" . urlencode($message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $smsresult = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            return response()->json(['error' => 'Failed to send SMS. Error: ' . curl_error($ch)], 500);
        }

        curl_close($ch);
    }

    public function verifyOtpAndCompleteRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->where('otp_expiry', '>', now()) // Ensure OTP is not expired
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        if ($user->status === 1) {
            return response()->json(['error' => 'User already active'], 400);
        }

        try {
            $user->otp = null;
            $user->status = 1;
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user', 'token'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while completing the registration'], 500);
        }
    }
}
