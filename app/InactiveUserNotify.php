<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\User;
use Mail;
use App\Mail\InactiveUserFirstMail;
use App\Mail\InactiveUserSecondMail;
use App\Mail\InactiveUserBeneficiaryMail;
use App\Services\OTPService;
use Carbon\Carbon;

class InactiveUserNotify extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }


    public function sendEmailAutomation($userid_list){
        $user_list = User::whereIn('id',$userid_list)->get();
        foreach($user_list as $user){
            $inactive_user_notify = $user->inactive_user_notify;
            $last_login = $inactive_user_notify->last_login;
            $first_send_email = $inactive_user_notify->first_send_email;
            $second_send_email = $inactive_user_notify->second_send_email;
            $send_sms = $inactive_user_notify->send_sms;
            $send_email_beneficiary_user = $inactive_user_notify->send_email_beneficiary_user;
            $send_sms_beneficiary_user = $inactive_user_notify->send_sms_beneficiary_user;
            $final_make_call = $inactive_user_notify->final_make_call;
            
            $last_login_carbon = Carbon::parse($last_login);
            $now = Carbon::now();
            $diff_days = $last_login_carbon->diffInDays($now);

            if($diff_days >60){
                if(empty($first_send_email)){
                    Mail::to($user->email)->send(new InactiveUserFirstMail($user));
                    $users[]= $user->email;
                    $inactive_user_notify = $user->inactive_user_notify;
                    $inactive_user_notify->first_send_email = Carbon::now();
                    $inactive_user_notify->save();
                    return "first email";
                }else{
                    $first_email = Carbon::parse($inactive_user_notify->first_send_email);
                    $diff_days = $first_email->diffInDays($now);
                    if($diff_days>7){
                        if(empty($second_send_email))
                        {
                            Mail::to($user->email)->send(new InactiveUserFirstMail($user));
                            $users[]= $user->email;
                            $inactive_user_notify = $user->inactive_user_notify;
                            $inactive_user_notify->second_send_email = Carbon::now();
                            $inactive_user_notify->save();
                            return "second email";
                        }else{
                            $second_email = Carbon::parse($inactive_user_notify->second_send_email);
                            $diff_days_second_mail = $second_email->diffInDays($now);
                            if($diff_days_second_mail>7){
                                if(empty( $send_sms)){
                                    $otpService = new OtpService;
                                    $sendStatus = $otpService->sendSMSInactiveUserPrimary($user->mobile);
                                    if($sendStatus){
                                        $statusSMS = "sms is sent successfully!";
                                    }else{
                                        $statusSMS = "sms is not sent!";
                                    }

                                    $users[$user->id]= $statusSMS ;
                                    $inactive_user_notify = $user->inactive_user_notify;
                                    $inactive_user_notify->send_sms = Carbon::now();
                                    $inactive_user_notify->save();
                                    return "send sms";
                                }else{
                                    $send_sms_user = Carbon::parse($send_sms);
                                    $diff_days_sms = $send_sms_user->diffInDays($now);
                                    if($diff_days_sms>7){
                                        if(empty($send_email_beneficiary_user)){
                                            $this->sendEmailBeneficiary([$user->id]);
                                            return "send email beneficiary";
                                        }else{
                                            $send_email_beneficiary = Carbon::parse($send_email_beneficiary_user);
                                            $diff_days_send_email_beneficiary = $send_email_beneficiary->diffInDays($now);
                                            if($diff_days_send_email_beneficiary>7){
                                                if(empty($send_sms_beneficiary_user)){
                                                    $this->sendSMSBeneficiary([$user->id]);
                                                    return "send sms beneficiary";
                                                }else{
                                                    $send_sms_beneficiary = Carbon::parse($send_sms_beneficiary_user);
                                                    $diff_days_sms_beneficiary = $send_sms_beneficiary->diffInDays($now);
                                                    if($diff_days_sms_beneficiary>7){
                                                        if(empty($final_make_call)){
                                                            Mail::to($user->email)->send(new InactiveUserFirstMail($user));
                                                            $users[]= $user->email;
                                                            $inactive_user_notify = $user->inactive_user_notify;
                                                            $inactive_user_notify->final_make_call = Carbon::now();
                                                            $inactive_user_notify->save();
                                                            return "final make call";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }
                       
                    }
                }
            }
            return $diff_days;

            // Mail::to($user->email)->send(new InactiveUserFirstMail($user));
            // $users[]= $user->email;
            // $inactive_user_notify = $user->inactive_user_notify;
            // $inactive_user_notify->first_send_email = Carbon::now();
            // $inactive_user_notify->save();
        }
            
    }

    public function sendFirstEmailPrimary($userid_list){
        $user_list = User::whereIn('id',$userid_list)->get();
        foreach($user_list as $user){
            Mail::to($user->email)->send(new InactiveUserFirstMail($user));
            $users[]= $user->email;
            $inactive_user_notify = $user->inactive_user_notify;
            $inactive_user_notify->first_send_email = Carbon::now();
            $inactive_user_notify->save();
        }
            
    }
        
    public function sendSecondEmailPrimary($userid_list){  
        $user_list = User::whereIn('id',$userid_list)->get();
        foreach($user_list as $user){
            Mail::to($user->email)->send(new InactiveUserSecondMail($user));
            $users[]= $user->email;
            $inactive_user_notify = $user->inactive_user_notify;
            $inactive_user_notify->second_send_email = Carbon::now();
            $inactive_user_notify->save();
        }
    }

    public function sendSMSPrimary($userid_list){
        $user_list = User::whereIn('id',$userid_list)->get();
        $otpService = new OTPService;
        foreach($user_list as $user){
            
            $sendStatus = $otpService->sendSMSInactiveUserPrimary($user->mobile);
            if($sendStatus){
                $statusSMS = "OTP is sent success!";
            }else{
                $statusSMS = "OTP is not sent!";
            }

            $users[$user->id]= $statusSMS ;
            $inactive_user_notify = $user->inactive_user_notify;
            $inactive_user_notify->send_sms = Carbon::now();
            $inactive_user_notify->save();
        }
        return $users;

    }
            
    public function sendEmailBeneficiary($userid_list){ 
        $beneficiary_user_list = User::whereIn('beneficiary_id',$userid_list)->get();
        foreach($beneficiary_user_list as $user){
            Mail::to($user->email)->send(new InactiveUserBeneficiaryMail($user));
            $users[]= $user->email;
            $inactive_user_notify = $user->inactive_user_notify;
            if(!empty($inactive_user_notify)){
                $inactive_user_notify->send_email_beneficiary_user = Carbon::now();
                $inactive_user_notify->save();
            }
        }
        return $users;
    }
        
    public function sendSMSBeneficiary($userid_list){
        $user_list = User::whereIn('beneficiary_id',$userid_list)->get();
        $otpService = new OTPService;
        foreach($user_list as $user){
            $user_name = Crypt::decryptString($user->name);
            $sendStatus = $otpService->sendSMSInactiveUserBeneficiary($user->mobile,$user_name);
            if($sendStatus){
                $statusSMS = "OTP is sent success!";
            }else{
                $statusSMS = "OTP is not sent!";
            }
            $users[$user->id]= $statusSMS ;
            $inactive_user_notify = $user->inactive_user_notify;
            $inactive_user_notify->send_sms_beneficiary_user = Carbon::now();
            $inactive_user_notify->save();
        }
        return $users;
    }
        
    public function finalMakeCallPrimary($userid_list){
        $user_list = User::whereIn('id',$userid_list)->get();
        foreach($user_list as $user){
            Mail::to($user->email)->send(new InactiveUserFirstMail($user));
            $users[]= $user->email;
            $inactive_user_notify = $user->inactive_user_notify;
            $inactive_user_notify->final_make_call = Carbon::now();
            $inactive_user_notify->save();
        } 
    }
}
