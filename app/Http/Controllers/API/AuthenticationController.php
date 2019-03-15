<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
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
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'user_name' => $user->name,
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
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ], 200);
    }

    
}