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


class AuthenticationController extends BaseController
{
    public function login(Request $request){
        $user = User::where('email', '=', $request->email)->first();
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
                
                return response()->json([
                    'message' => 'User logged in successfully!',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
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
     
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
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
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $tokenResult = $user->createToken('ThisHeartAccessToken');
        $success['name'] =  $user->name;

        return response()->json([
            'message' => 'User registered successfully!',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_name' => $user->name,
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
            'beneficiary_id' => 'required',
            'last4social_code' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $beneficiaryInfo = Beneficiary::where('id', '=', $request->beneficiary_id)->first();
        if($beneficiaryInfo){

            if($beneficiaryInfo->last_4_beneficiary == $request->last4social_code){
            }else{
                return response()->json([
                    'message' => 'Invalid social code. Please try again.',
                    'validated' => 0
                ],400);
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
                'message' => 'You have already account with another email id.',
            ], 406);
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
                    'user_name' => $beneficiaryInfo->first_name ." ".$beneficiaryInfo->last_name,
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

    
}