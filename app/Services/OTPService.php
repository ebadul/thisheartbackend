<?php 

namespace App\Services;

// use Illuminate\Support\ServiceProvider;
// use Twilio\Rest\Client;
use Mail;
use Auth;
use App\User;
use App\OtpSetting;
use App\OtpCode;
use App\Mail\OTPMail;
use Hash;
use Carbon\Carbon;

class OTPService{

    protected $twilio;

    public function __construct()
    {
        $this->twilio = app('twilio');
    }

    public function getOTPCode()
    {
       return rand(100000,999999);
    }
    
    public function isExistsOTP(User $user, $request){
        $user_id = $user->id;
        $otp_setting = $user->OTPSetting;
        if(empty($otp_setting)){
           return "not found";   
        }else{
            if($otp_setting->otp_enable){
                return "enable";
            }else{
                return "disable";
            }
        }
    }

    public function isEnableOTP(OTPSetting $otpSetting){
        if($otpSetting->otp_enable){
           return true;   
        }else{
            return false;
        }
    }

    public function createOtpSMS($user){
         
            $otp_code = $this->getOTPCode();
            if($user->OtpCode()->exists()){
                $otp_data = $user->OtpCode;
                $otp_data->otp_code =  $otp_code;
                $otp_data->verified =  false;
                $otp_data->expired =  false;
                $otp_data->expired_time =  Carbon::now()->addSeconds(60);//30 seconds add
                $otp_data->save();
            }else{
                $user->OtpCode()->create([
                    'user_id'=> $user->id,
                    'user_type'=>!empty($user->user_types->id)?$user->user_types->id:0,
                    'otp_code'=>$otp_code,
                    'verified'=>false,
                    'expired'=>false
                ]);
            }
            $mobile_no = $user->mobile;
            $sentSMS = $this->sendSMS($mobile_no,$otp_code);
            return $sentSMS;
    }

    public function createOtpEmail($user){
         
        $otp_code = $this->getOTPCode();
        if($user->OtpCode()->exists()){
            $otp_data = $user->OtpCode;
            $otp_data->otp_code =  $otp_code;
            $otp_data->verified =  false;
            $otp_data->expired =  false;
            $otp_data->expired_time =  Carbon::now()->addSeconds(60);//30 seconds add
            $otp_data->save();
        }else{
            $user->OtpCode()->create([
                'user_id'=> $user->id,
                'user_type'=>!empty($user->user_types->id)?$user->user_types->id:0,
                'otp_code'=>$otp_code,
                'verified'=>false,
                'expired'=>false
            ]);
        }
        $emailSent = $this->sendEmail($user,$otp_code);
        return $emailSent ;
    }

    public function createQRCodeGoogle($user, OTPSetting $otpSettingData){
        $google_key = $this->generateGoogleKey();
        $otpSettingData->google_key = $google_key;
        $otpSettingData->save();
        $google_qrcode_url = $this->generateGoogleQRCode($user, $otpSettingData);
        return $google_qrcode_url;
    }

    public function generateFirstTimeOTP(User $user, $request){
        $user_id = $user->id;
        
        if(empty($user_id)){
            throw new Exception("User not found!");
        }
        $isExists = $this->isExistsOTP($user, $request);
        $otp_setting=null;
        if($isExists==="not found"){
            $otp_setting = OTPSetting::create([
                'user_id'=>$user_id,
                'otp_method'=>$request->otp_method,
                'otp_enable'=>true,
            ]);  
        }else{
            $otp_setting = $user->OTPSetting;
        }
        if(!empty($otp_setting)){
            $otp_enable = $this->isEnableOTP($otp_setting);
            if(!$otp_enable){
                empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                $user->OTPSetting->otp_enable=true;
                $user->OTPSetting->save();
            }
        }
        
        if($request->otp_method==="sms"){
            if(empty($user->mobile)){
                return [
                    'status'=>'error',
                    'method'=>'sms',
                    'message'=>'Mobile number is not found!',
                    'data'=>null
                    ];
            }
            $sms=$this->createOtpSMS($user);
            return [
                'status'=>'success',
                'method'=>'sms',
                'data'=>$sms
                ];

        }elseif($request->otp_method==="email"){
            $emailSent = $this->createOtpEmail($user);
            return [
                'status'=>'success',
                'method'=>'email',
                'data'=>$emailSent
                ];
        }elseif($request->otp_method==="googleauth"){
            $google_qrcode_url = $this->createQRCodeGoogle($user, $otp_setting);
            return [
                'status'=>'success',
                'method'=>'googleauth',
                'data'=>$google_qrcode_url
                ];
        }
    }

    public function generateSecondTimeOTP(User $user, $request){
        $user_id = $user->id;

        if(empty($user_id)){
            throw new Exception("User not found!");
        }
        $isExists = $this->isExistsOTP($user, $request);
        $otp_setting=null;
        if($isExists==="not found"){
            return [
                'status'=>'error',
                'message'=>'OTP settings is not found',
                'data'=>null
                ];
        }else{
            $otp_setting = $user->OTPSetting;
        }
        if(!empty($otp_setting)){
            $otp_enable = $this->isEnableOTP($otp_setting);
            // if(!$otp_enable){
                // empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                $user->OTPSetting->otp_enable=true;
                $user->OTPSetting->save();
            // }
        }
        
        if($otp_setting->otp_method==="sms"){
            $user->mobile = $request->mobile;
            $user->save();
            if(empty($user->mobile)){
                return [
                    'status'=>'error',
                    'method'=>'sms',
                    'message'=>'Mobile number is not found!',
                    'data'=>null
                    ];
            }
            $this->createOtpSMS($user);
            return [
                'status'=>'success',
                'method'=>'sms',
                'data'=>null
                ];

        }elseif($otp_setting->otp_method==="email"){
            $emailSent = $this->createOtpEmail($user);
            return [
                'status'=>'success',
                'method'=>'email',
                'data'=>$emailSent
                ];
        }elseif($otp_setting->otp_method==="googleauth"){
            $google_qrcode_url = $this->createQRCodeGoogle($user, $otp_setting);
            return [
                'status'=>'success',
                'method'=>'googleauth',
                'data'=>$google_qrcode_url
                ];
        }
    }

    public function resetGenerateFirstTimeOTP(User $user, $request){
        $user_id = $user->id;
        if(empty($user_id)){
            throw new Exception("User not found!");
        }
        $isExists = $this->isExistsOTP($user, $request);
        $otp_setting=null;
        if($isExists==="not found"){
            $otp_setting = OTPSetting::create([
                'user_id'=>$user_id,
                'otp_method'=>$request->otp_method,
                'otp_enable'=>true,
            ]);  
        }else{
            $otp_setting = $user->OTPSetting;
        }
        if(!empty($otp_setting)){
            // $otp_enable = $this->isEnableOTP($otp_setting);
            // if(!$otp_enable){
                empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                $user->OTPSetting->otp_enable=true;
                $user->OTPSetting->save();
            // }
        }
        if($request->otp_method==="sms"){
            if(empty($user->mobile)){
                return [
                    'status'=>'error',
                    'method'=>'sms',
                    'message'=>'Mobile number is not found!',
                    'data'=>null
                    ];
            }
            $this->createOtpSMS($user);
            return [
                'status'=>'success',
                'method'=>'sms',
                'data'=>null
                ];

        }elseif($request->otp_method==="email"){
            $emailSent = $this->createOtpEmail($user);
            return [
                'status'=>'success',
                'method'=>'email',
                'data'=>$emailSent
                ];
        }elseif($request->otp_method==="googleauth"){
            $google_qrcode_url = $this->createQRCodeGoogle($user, $otp_setting);
            return [
                'status'=>'success',
                'method'=>'googleauth',
                'data'=>$google_qrcode_url
                ];
        }
    }

    public function resetGenerateSecondTimeOTP(User $user, $request){
        $user_id = $user->id;
        if(empty($user_id)){
            throw new Exception("User not found!");
        }
        $isExists = $this->isExistsOTP($user, $request);
        $otp_setting=null;
        if($isExists==="not found"){
            return [
                'status'=>'error',
                'message'=>'OTP settings is not found',
                'data'=>null
                ];
        }else{
            $otp_setting = $user->OTPSetting;
        }
        if(!empty($otp_setting)){
            // $otp_enable = $this->isEnableOTP($otp_setting);
            // if(!$otp_enable){
                //empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                empty($request->otp_method)?true:$user->OTPSetting->otp_method=$request->otp_method;
                $user->OTPSetting->otp_enable=true;
                $user->OTPSetting->save();
            // }
        }
         
        if($otp_setting->otp_method==="sms"){
            $user->mobile = $request->mobile;
            $user->save();
            if(empty($user->mobile)){
                return [
                    'status'=>'error',
                    'method'=>'sms',
                    'message'=>'Mobile number is not found!',
                    'data'=>null
                    ];
            }
            $this->createOtpSMS($user);
            return [
                'status'=>'success',
                'method'=>'sms',
                'data'=>null
                ];

        }elseif($otp_setting->otp_method==="email"){
            $emailSent = $this->createOtpEmail($user);
            return [
                'status'=>'success',
                'method'=>'email',
                'data'=>$emailSent
                ];
        }elseif($otp_setting->otp_method==="googleauth"){
            $google_qrcode_url = $this->createQRCodeGoogle($user, $otp_setting);
            return [
                'status'=>'success',
                'method'=>'googleauth',
                'data'=>$google_qrcode_url
                ];
        }
    }


    public function enableOTP($user,$user_type, $request){
        $user_id = $user->id;
        if(empty($user_id)){
            throw new Exception("User not found!");
        }
       
        if($request->step==1){
            if($request->otp_method==="sms"){
                $otp_code = $this->getOTPCode();
                if($user->OtpCode()->exists()){
                    $otp_data = $user->OtpCode;
                    $otp_data->otp_code =  $otp_code;
                    $otp_data->verified =  false;
                    $otp_data->expired =  false;
                    $otp_data->expired_time =  Carbon::now()->addSeconds(60);//30 seconds add
                    $otp_data->save();
                }else{
                    $user->OtpCode()->create([
                        'user_id'=> $user->id,
                        'user_type'=>!empty($user->user_types->id)?$user->user_types->id:0,
                        'otp_code'=>$otp_code,
                        'verified'=>false,
                        'expired'=>false
                    ]);
                }
                $this->sendSMS($user->mobile,$otp_code);
               
            }elseif($request->otp_method==="email"){
                $otp_code = $this->getOTPCode();
                if($user->OtpCode()->exists()){
                    $otp_data = $user->OtpCode;
                    $otp_data->otp_code =  $otp_code;
                    $otp_data->verified =  false;
                    $otp_data->expired =  false;
                    $otp_data->expired_time =  Carbon::now()->addSeconds(60);//30 seconds add
                    $otp_data->save();
                }else{
                    $user->OtpCode()->create([
                        'user_id'=> $user->id,
                        'otp_code'=>$otp_code,
                        'verified'=>false,
                        'expired'=>false
                    ]);
                }
                $emailSent = $this->sendEmail($user,$otp_code);
                
            }elseif($request->otp_method==="googleauth"){
                $google_key = $this->generateGoogleKey();
                $otpSettingData = $user->OTPSetting;
                $otpSettingData->google_key = $google_key;
                $otpSettingData->save();
                $google_qrcode_url = $this->generateGoogleQRCode($user, $otpSettingData);
            }
            return [
                'status'=>'success',
                'message'=>'step one done',
                'otp_method'=>$request->otp_method,
                'data'=>isset($google_qrcode_url)?$google_qrcode_url:null,
                'step'=>1
            ];
        }elseif($request->step==2){
            $verify = $this->verifyCode($user,$user_type, $request);
            if($verify['status']=='success'){
                $otp_setting = $user->OTPSetting;
                if(empty($otp_setting)){
                    $otp_setting = OTPSetting::create([
                        'user_id'=>$user_id,
                        'otp_method'=>$request->otp_method,
                        'otp_enable'=>true,
                    ]);     
                }else{
                    $user->OTPSetting->otp_enable = true;
                    $user->OTPSetting->save();
                }

                return [
                    'status'=>'success',
                    'message'=>'OTP has enabled successfully',
                    'otp_method'=>$request->otp_method,
                    'data'=>null
                ];
            }else{
                $verify['otp_method'] = $request->otp_method;
                return $verify;
            }
        }        
    }

    public function disableOTP($user, $request){

        if($user->OTPSetting()->exists()){
            $user->OTPSetting->otp_enable = false;
            $user->OTPSetting->save();
            return [
                'status'=>'success',
                'message' => 'OTP is disabled'
            ];
        }else{
            return [
                'status'=>'error',
                'message' => 'Sorry, problem to disable OTP service.'
            ];
        }
        
    }

    public function generateGoogleKey(){
        $google2fa = app('pragmarx.google2fa');
        $google_key = $google2fa->generateSecretKey();
        if($google_key){
            return $google_key;
        }else{
            throw new Exception('Google key is not generated');
        }
    }

    public function verifyCode($user, $request){
        if($request->otp_method==="googleauth"){
            $google2fa = app('pragmarx.google2fa');
            $valid = $google2fa->verifyKey($user->OTPSetting->google_key, $request->otp_code);
            if($valid){
                return [
                    'status'=>'success',
                    'message'=>'Verify code has been checked successfully',
                    'data'=>null
                ];
            }else{
                $google2fa = null;
                return [
                    'status'=>'error',
                    'message'=>'Verify code has some error',
                    'data'=>null
                ];
            }
        }elseif($request->otp_method==="sms_email"){
            $user_type_id = $user->user_types->id; 
            $otpData = OtpCode::where('user_id', $user->id)->
                       where('user_type',$user_type_id)->
                       where('otp_code',$request->otp_code)->
                       where('verified',false)->
                       where('expired',false)->first();
            
            if($otpData){
                //$expired_time = $otpData->expired_time;
                //$now_time = Carbon::now();
                // if($now_time>$expired_time){
                //     return [
                //         'status'=>'error',
                //         'message'=>'Sorry, OTP is verified failed',
                //         'data'=>null
                //     ];
                // }
                $otpData->verified = true; 
                $otpData ->expired = true; 
                $otpData ->save(); 
                return [
                    'status'=>'success',
                    'message'=>'OTP is verified successfully',
                    'data'=>null
                ];
            }else
            {
                return [
                    'status'=>'error',
                    'message'=>'Sorry, OTP is verified failed',
                    'data'=>null
                ];
            }
        }
    }

    public function generateGoogleQRCode($user, OTPSetting $otpSetting){
        $google2fa = app('pragmarx.google2fa');
        $google2fa->setAllowInsecureCallToGoogleApis(true);
        $google_key= $otpSetting->google_key;
        if(empty($google_key))
        {
            $google_key = $this->generateGoogleKey();
            $otpSetting->google_key = $google_key;
            $otpSetting->save();
        }
        $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                'This Heart',
                $user->email,
                $google_key);
        
        if(empty($google2fa_url)){
            throw new Exception('Google QR Code is not generated');
        }else{
            return $google2fa_url;
        }
    }

    public function selectOTPMethod($method){

    }

    public function changeOTPMethod($method){
        
    }

    public function sendSMS($to,$otp)
    {
        $accountSid = env('TWILIO_SID');
        $authToken = env('TWILIO_TOKEN');
        $twilioNumber = env('TWILIO_NUMBER');

        try {         
            $this->twilio->messages->create(
                $to,
                [
                    "body" => 'OTP is : '.$otp,
                    "from" => $twilioNumber
                ]
            );    
        } catch (TwilioException $e) {
            return new \Exception($e->getMessage().'- This Hearts');
        }
        return true;
    }

    public function sendEmail($user,$otp)
    {
        try{
            if(Mail::to($user->email)->send(new OTPMail($user, $otp))){
                return true;  
            }else{
                return false;
            }
        }catch(Exception $ex){
            return [
                'status'=>'error',
                'message'=>$ex->getMessage()
            ];
        }     
    }
        
       
       

}