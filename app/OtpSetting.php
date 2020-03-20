<?php

namespace App;
use Auth;
use App\Services\OTPService;
use Illuminate\Database\Eloquent\Model;

class OtpSetting extends Model
{
    protected $fillable = ['user_id','user_type','otp_method','otp_enable','google_key'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function user_type(){
        return $this->belongsTo(UserType::class);
    }

    public function sendWelcomeSMS($mobile){
        try{
            $otpService = new OTPService;
            $msg="Welcome to thisheart.co";
            $sendStatus = $otpService->sendSMS($mobile,$msg);
            if($sendStatus){
                return "success";
            }else{
                return "error";
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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

}
