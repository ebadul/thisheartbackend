<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Beneficiary;
use App\BeneficiaryUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;


class AuthenticationController extends BaseController
{
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
                'message' => 'Email not exist!',
                'code'=>'email'
            ], 401);
        }else{
            
            $passwordOK = Hash::check($request->password, $user->password);
            if($passwordOK){

                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                    $user = Auth::user();
                    $tokenResult = $user->createToken('ThisHeartAccessToken');
                    $accountProgressStatus = true;
                    //Check all account progress data.
                    //$user->forceFill(['token'=>$tokenResult->accessToken])->save();
                    $accountProgressStatus = $this->checkAccountProgressData($user->id);
                   
                    return response()->json([
                        'status' => 'success',
                        'message' => 'User logged in successfully!',
                        'user_id' => $user->id,
                        'user_name' => Crypt::decryptString($user->name),
                        'access_token' => $tokenResult->accessToken,
                        'account_progress_status' => $accountProgressStatus,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                        'data'=>$user,
                        'primary_user_id'=>$user->beneficiary_id,
                        'user_type'=>!empty($user->user_types->user_type)?$user->user_types->user_type:'',
                        'profile_image'=>!empty($user->image_list[0]->image_url)?$user->image_list[0]->image_url:''
                    ], 200);
                }
                else{
                    return response()->json(['error'=>'Unauthorised'], 401);
                }
            
            }else{
                return response()->json([
                    'status'=>'error',
                    'message' => 'You entered wrong password!'
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|unique:users',
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

        $userData = User::where('email', '=', $request->email)->first();
        if($userData){
            return response()->json([
                'status'=>'fail',
                'message' => 'Email already exist. Please use another.',
            ], 200);
        }

        $userTmp = new User; 
        $input = $request->all();
        $input['name'] = Crypt::encryptString($request->name);
        $input['password'] = bcrypt($input['password']);
        $input['user_type'] = $userTmp->getUserTypeID('primary');
        $user = User::create($input);
        $tokenResult = $user->createToken('ThisHeartAccessToken');

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

    public function registerBeneficiaryUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|unique:users',
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
        //Log::info("Email = ".$request->email);
        //Log::info("Password = ".$request->password);

        if($user === null){
            return response()->json([
                'message' => 'Email not exist!',
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
                    'message' => 'You enter wrong password!'
                ], 422);
            }
        }
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