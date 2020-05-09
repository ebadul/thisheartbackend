<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Beneficiary;
use App\BeneficiaryUser;
use App\EmailVerification;
use App\UserActivity;
use App\OtpSetting;
use App\InactiveUserNotify;
use App\PackageInfo;
use App\UserPackage;
use App\Mail\MailNotifyFifteenDaysMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Mail;


class AuthenticationController extends BaseController
{
    // protected $access_url = "http://45.35.50.179/";
    protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
    }

    public function login(Request $request){
        
        $user = User::where('email', '=', $request->email)->first();
        if(!empty($user)){
            $user_type = $user->user_types->user_type;
            if($user_type==="primary" || $user_type==="beneficiary"){
            }else{
                return response()->json([
                    'status'=>'error',
                    'message' => 'This user type has no permission!',
                    'code'=>'user_type'
                ], 400);
            }
        }
        
        if($user === null){
            return response()->json([
                'status'=>'error',
                'message' => 'Sorry, that didn’t work. Please try again',
                'code'=>'email'
            ], 401);
        }else{

            if($user->email_verified===0){
                return response()->json([
                    'status'=>'error',
                    'message' => "Sorry, this email isn't verified",
                    'code'=>'email'
                ], 401);
            }

            if($user->active===0){
                return response()->json([
                    'status'=>'error',
                    'message' => "Sorry, user isn't actived",
                    'code'=>'email'
                ], 401);
            }

            
            $passwordOK = Hash::check($request->password, $user->password);
            if($passwordOK){

                if(Auth::attempt(['email' => $request->email, 'password' => $request->password,'email_verified'=>1])){
                    $user = Auth::user();
                    $tokenResult = $user->createToken('ThisHeartAccessToken');
                    $accountProgressStatus = true;
                    $accountProgressStatus = $this->checkAccountProgressData($user->id);
                    $checkAccountWizard = $this->checkAccountWizard($user->id);
                   
                    $user_id = $user->id;
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $platform = $_SERVER['HTTP_USER_AGENT'];
                    $user_activity = new UserActivity;
                    $user_activity->user_id = $user_id;
                    $user_activity->ip = $ip;
                    $user_activity->platform = json_encode($platform);
                    $user_activity->save();

                    $inactive_user_notify =  InactiveUserNotify::where('user_id',$user->id)->first();
                    if(empty($inactive_user_notify)){
                        $inactive_user_notify = new InactiveUserNotify;
                        $inactive_user_notify->user_id = $user->id;
                    }
                        
                        $inactive_user_notify->last_login = Carbon::now();
                        $inactive_user_notify->first_send_email = null;
                        $inactive_user_notify->second_send_email = null;
                        $inactive_user_notify->send_sms = null;
                        $inactive_user_notify->send_email_beneficiary_user = null;
                        $inactive_user_notify->send_sms_beneficiary_user = null;
                        $inactive_user_notify->final_make_call = null;
                        $inactive_user_notify->save();
                   
                    $user->last_login=Carbon::now();
                    $user->save();

                    $user_pkg = $user->user_package;
                    if(!empty( $user_pkg)){
                        $now = Carbon::now();
                        $expire_date = Carbon::parse($user_pkg->subscription_expire_date);
                        $diff = $expire_date->diffInDays($now);
                        $user_pkg->push('package_info',$user_pkg->package_info);
                        $user_pkg->access_url = $this->access_url;
                        $user_pkg->remaining_days = $diff;
                        $user_pkg->encryptedString = Crypt::encryptString('packageSubscription');
                        if($now > $expire_date){
                            return response()->json([
                                'status'=>'error',
                                'message' => 'This user package subscription is expired!',
                                'code'=>'user_type',
                            ], 400);
                        }else{
                            if($diff<16){
                                if(!$inactive_user_notify->package_expire_notify){
                                    $inactive_user_notify->package_expire_notify = 1;
                                    $inactive_user_notify->save();
                                    Mail::to($user->email)->send(new MailNotifyFifteenDaysMail($user, $user_pkg));
                                }
                            }
                        }
                        
                        //$user_pkg->push('package_info',$user_pkg->package_info);
                    }

                    $user_type = $user->user_types->user_type;
                    if($user_type==="beneficiary"){
                        $primary_user = $user->primary_user ;
                        if($primary_user){
                            $primary_user->name = Crypt::decryptString($primary_user->name);
                        } 
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'User logged in successfully!',
                        'user_id' => $user->id,
                        'user_name' => Crypt::decryptString($user->name),
                        'access_token' => $tokenResult->accessToken,
                        'account_progress_status' => $accountProgressStatus,
                        'account_wizard' => $checkAccountWizard,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                        'data'=>$user,
                        'sub_plan'=>$user_pkg,
                        'primary_user'=>$user->primary_user,
                        'primary_user_id'=>$user->beneficiary_id,
                        'user_type'=>!empty($user->user_types->user_type)?$user->user_types->user_type:'',
                        'profile_image'=>!empty($user->image_list[0]->image_url)?$user->image_list[0]->image_url:''
                    ], 200);
                }
                else{
                    return response()->json([
                        'error'=>'Unauthorised',
                        'message' => 'Sorry, that didn’t work. Please try again',
                    ], 401);
                }
            
            }else{
                return response()->json([
                    'status'=>'error',
                    'message' => 'Sorry, that didn’t work. Try again',
                    'password'=>$passwordOK
                ], 422);
            }
        }
    }
     
    public function logout()
    {
        Auth::user()->token()->revoke();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function register(Request $request)
    {
        $requests = [
            'email'=>base64_decode($request->email),
            'name'=>base64_decode($request->name),
            'password'=>base64_decode($request->password),
            'beneficiary_id'=>base64_decode($request->beneficiary_id),
        ];

        $validator = Validator::make($requests, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>'fail',
                'message' => 'Sorry, user registration is failed!',
                'data'=>$validator->errors()
            ], 200);
            //return $this->sendError('Validation Error.', $validator->errors());       
        }

        
        $requests = (object)$requests;
        $userData = User::where('email', '=', $requests->email)->first();
        if($userData){
            return response()->json([
                'status'=>'fail',
                'message' => 'Email already exist. Please use another.',
            ], 400);
        }

        $userTmp = new User; 
        $input = (array)$requests;

        $input['name'] = Crypt::encryptString($requests->name);
        $input['password'] = bcrypt($input['password']);
        $input['user_type'] = $userTmp->getUserTypeID('primary');
        $user = User::create($input);
        $tokenResult = $user->createToken('ThisHeartAccessToken');
        $data = $user;
        $url_token= str_random(16);
        $email_str = Crypt::encryptString($user->email);
        $data['login_url'] = $this->access_url.'email_verification/'.$url_token.'/'.$email_str;
        $data['email_str'] = $email_str;
        // 'user_id','verified_token','email_verified'
        $emailVerifiedData = [
            'user_id'=>$user->id,
            'verified_token'=> $url_token,
            'email_verified'=> 0
        ];
        EmailVerification::create($emailVerifiedData);
        $to_name = $requests->name;
        $to_email = $requests->email;
        $userEmail = array_merge($data->toArray(),$emailVerifiedData);
        Mail::send('emails.register-primary-user', $userEmail, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('[thisheart.co] Activate your account');
            $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
        });

        $sub_plan = PackageInfo::where('package','=','Trial Package')->first();
        
        if(!empty($sub_plan)){
            $user_id = $user->id;
            $pkgData = [
                'user_id'=>$user_id,
                'package_id'=>$sub_plan->id
            ];
            $user_package = new UserPackage;
            $user_pkg = $user_package->saveUserPackage($pkgData);
            $user_pkg->push('package_info',$user_pkg->package_info);
            
        }
        

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_name' => Crypt::decryptString($user->name),
            'user_id' => $user->id,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'data'=>$user,
            'primary_user_id'=>$user->beneficiary_id,
            'user_type'=>!empty($user->user_types->user_type)?$user->user_types->user_type:'',
            'package_info'=>$user_pkg->package_info,
            'sub_plan'=>$user_pkg,
        ], 200);
    }

    public function email_verification($url_token, $email){
        if(empty($url_token) || empty($email)){
            return response()->json([
                'status'=>'error',
                'message'=>'Email verification informations are not valid'
            ],500);
        }
        $emailInfo = Crypt::decryptString($email);
        $userInfo = User::where('email',$emailInfo)->first();
        $tokenInfo = EmailVerification::where('verified_token',$url_token)->first();
        if(empty($userInfo) || empty($tokenInfo)){
            return response()->json([
                'status'=>'error',
                'message'=>'Email verification information is not valid'
            ],500);
        }elseif($userInfo->emailVerified->email_verified === 1){
            return response()->json([
                'status'=>'error',
                'message'=>'This email is activated already'
            ],500);
        }
        $userInfo->email_verified = 1;
        $userInfo->active = 1;
        $userInfo->save();

        $userInfo->emailVerified->email_verified = 1;
        $userInfo->emailVerified->save();

        return response()->json([
            'status'=>'success',
            'data'=>$userInfo
        ],200);

    }

    public function registerBeneficiaryUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            //'mobile' => 'unique:users',
            'password' => 'required',
            'user_id' => 'required',
            'beneficiary_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>'fail',
                'message' => 'Sorry, user registration is failed!',
                'data'=>$validator->errors(),
                'code'=>'invalid'
            ], 400);     
        }

        $beneficiaryInfo = Beneficiary::where('id', '=', $request->beneficiary_id)->first();
        if($beneficiaryInfo){

            if($beneficiaryInfo->validate_code == 0){
                return response()->json([
                    'status'=>'fail',
                    'message' => 'Your access code not validate yet. Please validate code then register.',
                    'code'=>'social'
                ],400);
            }

            if(Crypt::decryptString($beneficiaryInfo->last_4_beneficiary) == $request->last4social_code){
            }else{
                return response()->json([
                    'status'=>'fail',
                    'message' => 'Invalid social code. Please try again.',
                    'code'=>'social'
                ],400);
            }
           
        }

        $mobileData = User::where('mobile', '=', $request->mobile)->first();
        if(!empty($mobileData)){
            return response()->json([
                'status'=>'fail',
                'message' => 'Mobile number is used already. Please use another.',
                'code'=>'mobile'
            ], 400);
        }

        $otpSetting = new OtpSetting;
        $otpSettingStatus = $otpSetting->sendWelcomeSMS($request->mobile);
        if($otpSettingStatus!="success")
        {
            return response()->json([
                'data'=>'Unable to send sms to your mobile number!',
                'code'=>'mobile'
            ]);
        }
        


        $userData = BeneficiaryUser::where('email', '=', $request->email)->first();
        if($userData){
            return response()->json([
                'status'=>'fail',
                'message' => 'Email already exist. Please use another.',
                'code'=>'email'
            ], 400);
        }

        $userData = BeneficiaryUser::where('beneficiary_id', '=', $request->beneficiary_id)->first();
        if($userData){
            return response()->json([
                'status'=>'fail',
                'message' => 'You have already account ['.$userData->email.'] for this last 4 social code.',
                'code'=>'social'
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $beneficiaryUser = new BeneficiaryUser();
        $beneficiaryUser->email = $request->email;
        $beneficiaryUser->user_id = $request->user_id;
        $beneficiaryUser->beneficiary_id = $request->beneficiary_id;
        $beneficiaryUser->password = $input['password'];
        $beneficiaryUser->save();
        $userTmp = new User();
        $user_type_id = $userTmp->getUserTypeID('beneficiary');
        $regUser = [
                'name' => Crypt::encryptString($request->name),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => bcrypt($request->password),
                'beneficiary_id' => $request->user_id,
                'user_type' => $user_type_id,
            ];

        
        $user = User::create($regUser);
        $tokenResult = $user->createToken('ThisHeartAccessToken');

        $data = $user;
        $url_token= str_random(16);
        $email_str = Crypt::encryptString($user->email);
        $data['login_url'] = $this->access_url.'email_verification/'.$url_token.'/'.$email_str;
        $data['email_str'] = $email_str;
        // 'user_id','verified_token','email_verified'
        $emailVerifiedData = [
            'user_id'=>$user->id,
            'verified_token'=> $url_token,
            'email_verified'=> 0
        ];
        EmailVerification::create($emailVerifiedData);
        $to_name = $request->name;
        $to_email = $user->email;
        $userEmail = array_merge($data->toArray(),$emailVerifiedData);
        Mail::send('emails.register-primary-user', $userEmail, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('[thisheart.co] Activate your account');
            $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_name' => Crypt::decryptString($user->name),
            'user_id' => $user->id,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'data'=>$user,
            'primary_user_id'=>$user->beneficiary_id,
            'user_type'=>!empty($user->user_types->user_type)?$user->user_types->user_type:''
        ], 200);
    }

    public function loginBeneficiaryUser(Request $request){

        $user = BeneficiaryUser::where('email', '=', $request->email)->first();

        if($user === null){
            return response()->json([
                'message' => 'Sorry, that didn’t work. Try again3',
            ], 401);
        }else{
            $passwordOK = Hash::check($request->password, $user->password);
            if($passwordOK){
                $beneficiaryInfo = Beneficiary::findOrfail($user->beneficiary_id);
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                    $user = Auth::user();
                    $tokenResult = $user->createToken('ThisHeartAccessToken');
                }
                return response()->json([
                    'message' => 'User logged in successfully!',
                    'user_id' => $user->id,
                    'primary_user_id' => $user->user_id,
                    'user_name' => Crypt::decryptString($beneficiaryInfo->first_name) ." ".Crypt::decryptString($beneficiaryInfo->last_name),
                    'beneficiary_id' => $user->beneficiary_id,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Sorry, that didn’t work. Try again1'
                ], 422);
            }
        }
    }

    function checkAccountWizard($user_id){
        $accountWizard = DB::table('wizard_steps')->where('user_id','=',$user_id)->
        orderBy('steps')->
        get();
        return $accountWizard;
    }

    function checkAccountProgressData($user_id){

        $allDataCompleted = true;
        //Check Memories data
        $imageCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"image")
        ->select('memories.*')->count();
        $videoCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"video")
        ->select('memories.*')->count();
        $recordCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"record")
        ->select('memories.*')->count();
        $letterCount = DB::table('letters')->where('user_id','=',$user_id)->select('letters.*')->count();
        if($imageCount == 0 && $videoCount == 0 && $recordCount == 0 && $letterCount == 0){
            $allDataCompleted = false;
            Log::info("Memories data not filled up");
        }

        //Medical History data.
        $meCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Me")
        ->select('medical_histories.*')->count();
        $momCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Mom")
        ->select('medical_histories.*')->count();
        $dadCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Dad")
        ->select('medical_histories.*')->count();
        $partnerCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Partner")
        ->select('medical_histories.*')->count();
        if($meCount == 0 && $momCount == 0 && $dadCount == 0 && $partnerCount == 0){
            $allDataCompleted = false;
            Log::info("Medical data not filled up");
        }

        //Account data
        $accountInfoCount = DB::table('accounts')->where('user_id','=',$user_id)->select('accounts.*')->count();
        if($accountInfoCount == 0){
            $allDataCompleted = false;
            Log::info("Account data not filled up");
        }

        //Beneficiary data
        $beneficiaryInfoCount = DB::table('beneficiaries')->where('user_id','=',$user_id)->select('beneficiaries.*')->count();
        if($beneficiaryInfoCount == 0){
            $allDataCompleted = false;
            Log::info("Beneficiary data not filled up");
        }

        return $allDataCompleted;
    }
}