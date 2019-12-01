<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Services\OTPService;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    
    public function sendSMS($user){
        $otpService = new OTPService;
        $otpCode = $otpService->getOTPCode();
        $sendStatus = $otpService->sendSMS($user->mobile,$otpCode);
        if($sendStatus){
            echo "OTP sent success!";
        }else{
            echo "OTP not sent!";
        }
    }

    public function sendEmail(){
        $user = Auth::user();
        $otpService = new OTPService;
        $otpCode = $otpService->getOTPCode();
        $sendStatus = $otpService->sendEmail($user,$otpCode);
        if($sendStatus){
            echo "OTPMail sent success!";
        }else{
            echo "OTPMail not sent!";
        }
    }

    public function generateOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }

        $otpService = new OTPService;
        $otp_setting = $otpService->isExistsOTP($user, $request);
        
  
        if($otp_setting==="enable"){
            $otp_generate = $otpService->generateSecondTimeOTP($user, $request);
        }elseif($otp_setting==="not found"){
            $otp_generate = $otpService->generateFirstTimeOTP($user, $request);
        }elseif($otp_setting==="disable"){
            $otp_generate = $otpService->generateSecondTimeOTP($user, $request); 
        }

        if($otp_generate['status']==="success"){
            return response()->json([
                'status'=>'success',
                'method'=>$otp_generate['method'],
                'data'=>$otp_generate['data']
            ]);
        }elseif($otp_generate['status']==="otp_setting"){
            return response()->json([
                'status'=>'otp_setting',
                'message'=>'Request for new OTP settings!',
            ]);
        }elseif($otp_generate['status']==="disable"){
            return response()->json([
                'status'=>'disable',
                'message'=>'OTP is disable!',
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'OTP settings is not generated!',
            ]);
        }
    }

    public function resetGenerateOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }

        $otpService = new OTPService;
        $otp_setting = $otpService->isExistsOTP($user, $request);
        

        if($otp_setting==="enable"){
            $otp_generate = $otpService->resetGenerateSecondTimeOTP($user, $request);
        }elseif($otp_setting==="not found"){
            $otp_generate = $otpService->resetGenerateFirstTimeOTP($user, $request);
        }elseif($otp_setting==="disable"){
            $otp_generate = $otpService->resetGenerateSecondTimeOTP($user, $request); 
        }

        if($otp_generate['status']==="success"){
            return response()->json([
                'status'=>'success',
                'method'=>$otp_generate['method'],
                'data'=>$otp_generate['data']
            ]);
        }elseif($otp_generate['status']==="otp_setting"){
            return response()->json([
                'status'=>'otp_setting',
                'message'=>'Request for new OTP settings!',
            ]);
        }elseif($otp_generate['status']==="disable"){
            return response()->json([
                'status'=>'disable',
                'message'=>'OTP is disable!',
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'OTP is not generated!',
            ]);
        }
    }

    public function isExistsOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
       
        $otpService = new OTPService;
        $otp_setting = $otpService->isExistsOTP($user, $request);
        

        if($otp_setting==="enable"){
            $otp_tmp = $otpService->generateSecondTimeOTP($user, $request);
            if($otp_tmp['method']==="googleauth"){
                return response()->json([
                    'status'=>'success',
                    'data'=>'enable',
                    'method'=> $otp_tmp['method'] ,
                    'qr_url'=> $otp_tmp['data'] ,
                    'message'=>'generateSecondTimeOTP'
                ]);
            }else{
                return response()->json([
                    'status'=>'success',
                    'data'=>'enable',
                    'method'=> $otp_tmp['method'] ,
                    'message'=>'generateSecondTimeOTP'
                ]);
            }
        
        }elseif($otp_setting==="not found"){
            return response()->json([
                'status'=>'success',
                'data'=>'not-found',
                'message'=>'OTP Settings is not exists'
            ]);
        }elseif($otp_setting==="disable"){
            return response()->json([
                'status'=>'success',
                'data'=>$otp_setting,
                'message'=>'OTP is exists'
            ]);
        }
    }

    public function disableOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        
        $otpService = new OTPService;
        $otp_setting = $otpService->disableOTP($user, $request);
        return response()->json([
            'status'=> $otp_setting['status'],
            'message'=> $otp_setting['message']
        ]);
    }

    public function verifyCode(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        $validData = Validator::make($request->all(),[
            'otp_code'=>'required',
            'otp_method'=>'required',
        ]);

        if($validData->errors()->any()){
            return response()->json([
                'status'=>'error',
                'data'=>$validData->errors()
                ]);
        }
        $otpService = new OTPService;
        $otp_setting = $otpService->verifyCode($user, $request);
        return response()->json([
            'status'=> $otp_setting['status'],
            'message'=> $otp_setting['message'],
            'otp_method'=>$request->otp_method,
            'data'=> $otp_setting['data'],
            'user'=> $user,
        ]);
    }


    public function generateGoogleQRCode(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
                'data'=>null
            ]);
        }
        $otpService = new OTPService;
        $otpSettings = $user->OTPSettings;
        $google_qrcode_url = $otpService->generateGoogleQRCode($user, $otpSettings);
        return response()->json([
            'status'=> 'success',
            'message'=> '',
            'data'=> $google_qrcode_url,
        ]);
    }

    public function enableOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        $validData = Validator::make($request->all(),[
            'otp_method'=>'required',
            'step'=>'required'
        ]);

        if($validData->errors()->any()){
            return response()->json([
                'status'=>'error',
                'data'=>$validData->errors()
                ]);
        }
        $otpService = new OTPService;
        $otp_setting = $otpService->enableOTP($user, $request);
        return response()->json([
            'status'=> $otp_setting['status'],
            'message'=> $otp_setting['message'],
            'otp_method'=> $otp_setting['otp_method'],
            'data'=> $otp_setting['data'],
        ]);
    }

    public function checkPasswordOTP(Request $request){
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        $validData = Validator::make($request->all(),[
            'pass_word'=>'required',
        ]);

        if($validData->errors()->any()){
            return response()->json([
                'status'=>'error',
                'data'=>$validData->errors()
                ]);
        }
        $user = Auth::user();
        $user_password = $user->checkPasswordOTP($request);
        if($user_password)
        {
            return response()->json([
                'status'=> 'success',
                'message'=> 'password is matched',
                'data'=> '',
            ]);
        }else{
            return response()->json([
                'status'=> 'error',
                'message'=> 'sorry, password is not matching',
                'data'=> '',
            ]);
        }
        
    }





}
