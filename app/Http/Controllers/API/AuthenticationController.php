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
        Log::info("Email = ".$request->email);
        //Log::info("Password = ".$request->password);

        if($user === null){
            return response()->json([
                'message' => 'Email not exist!',
            ], 401);
        }else{
            
            $passwordOK = Hash::check($request->password, $user->password);
            if($passwordOK){
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                    $user = Auth::user();
                    $tokenResult = $user->createToken('ThisHeartAccessToken');
                    $accountProgressStatus = true;
                    //Check all account progress data.
                    $accountProgressStatus = $this->checkAccountProgressData($user->id);

                    return response()->json([
                        'message' => 'User logged in successfully!',
                        'user_id' => $user->id,
                        'user_name' => Crypt::decryptString($user->name),
                        'access_token' => $tokenResult->accessToken,
                        'account_progress_status' => $accountProgressStatus,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                    ], 200);
                }
                else{
                    return response()->json(['error'=>'Unauthorised'], 401);
                }
                
            }else{
                return response()->json([
                    'message' => 'Password mismatch!'
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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $userData = User::where('email', '=', $request->email)->first();
        if($userData){
            return response()->json([
                'message' => 'Email already exist. Please use another.',
            ], 406);
        }

        $input = $request->all();
        $input['name'] = Crypt::encryptString($request->name);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $tokenResult = $user->createToken('ThisHeartAccessToken');

        return response()->json([
            'message' => 'User registered successfully!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_name' => Crypt::decryptString($user->name),
            'user_id' => $user->id,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ], 200);
    }

    public function registerBeneficiaryUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'user_id' => 'required',
            'beneficiary_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $beneficiaryInfo = Beneficiary::where('id', '=', $request->beneficiary_id)->first();
        if($beneficiaryInfo){

            if($beneficiaryInfo->validate_code == 0){
                return response()->json([
                    'message' => 'Your access code not validate yet. Please validate code then register.'
                ],400);
            }

            if(Crypt::decryptString($beneficiaryInfo->last_4_beneficiary) == $request->last4social_code){
            }else{
                return response()->json([
                    'message' => 'Invalid social code. Please try again.'
                ],401);
            }
           
        }

        $userData = BeneficiaryUser::where('email', '=', $request->email)->first();
        if($userData){
            return response()->json([
                'message' => 'Email already exist. Please use another.',
            ], 406);
        }

        $userData = BeneficiaryUser::where('beneficiary_id', '=', $request->beneficiary_id)->first();
        if($userData){
            return response()->json([
                'message' => 'You have already account ['.$userData->email.'] for this last 4 social code.',
            ], 402);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $beneficiaryUser = new BeneficiaryUser();
        $beneficiaryUser->email = $request->email;
        $beneficiaryUser->user_id = $request->user_id;
        $beneficiaryUser->beneficiary_id = $request->beneficiary_id;
        $beneficiaryUser->password = $input['password'];
        $beneficiaryUser->save();

        return response()->json([
            'message' => 'User registered successfully!',
            'data' => $beneficiaryUser
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
                $tokenResult = $user->createToken('ThisHeartAccessToken');

                $beneficiaryInfo = Beneficiary::findOrfail($user->beneficiary_id);
                
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
                    'message' => 'Password mismatch!'
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