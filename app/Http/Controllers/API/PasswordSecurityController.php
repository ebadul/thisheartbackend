<?php

namespace App\Http\Controllers\API;

use App\User;
use App\PasswordSecurity;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Log;
use Crypt;
 
use Auth;
use Hash;
use Google2FA;

class PasswordSecurityController extends BaseController
{
    //
    public function getQRCode($user_id){
        $user = User::where('id', $user_id)->first();
		if(empty($user) )
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User info is empty!'
			],400);	
		}
		$google2fa_url = "";
        if($user->passwordSecurity()->exists()){
            $google2fa = app('pragmarx.google2fa');
            $google2fa->setAllowInsecureCallToGoogleApis(true);
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                '5Balloons 2FA DEMO',
                $user->email,
                $user->passwordSecurity->google2fa_secret
            );
        }
        return response()->json([
            'message' => 'User varified successfully!',
            'user' => $user,
            'google2fa_url' => $google2fa_url

        ],200);
    }
	
	public function getQRCodePost(Request $request){
        $user = User::where('id', $request->user_id)->first();
		if(empty($user) )
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User info is empty!'
			],400);	
		}
		$google2fa_url = "";
        if($user->passwordSecurity()->exists()){
            $google2fa = app('pragmarx.google2fa');
            $google2fa->setAllowInsecureCallToGoogleApis(true);
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                '5Balloons 2FA DEMO',
                $user->email,
                $user->passwordSecurity->google2fa_secret
            );
        }
        return response()->json([
            'message' => 'User varified successfully!',
            'user' => $user,
            'google2fa_url' => $google2fa_url

        ],200);
    }
	

    public function generate2faSecret(Request $request){
		$user_id = $request->user_id;
		
		if(empty($user_id) )
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User info is empty!'
			],400);	
		}
		
        $user = User::where('id', $user_id)->first();
		if(empty($user) )
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User info is invalid!'
			],400);	
		}
        $google2fa = app('pragmarx.google2fa');
    
        // Add the secret key to the registration data
        $passSecInfo = PasswordSecurity::where('user_id', '=', $request->user_id)->first();
        if(empty($passSecInfo)){
            $passSecInfo = PasswordSecurity::create([
                'user_id' => $user->id,
                'google2fa_enable' => 0,
                'google2fa_secret' => $google2fa->generateSecretKey(),
            ]);
        }
            
       
        return response()->json([
            'message' => 'Secret Key is generated, Please verify Code to Enable 2FA!, OK',
            'password_security' => $passSecInfo
        ],200);
    }

    public function enable2fa(Request $request){
		
		$user_id = $request->user_id;
		$verify_code = $request->verify_code;
		if(empty($user_id) || empty($verify_code))
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User/verify code is invalid!'
			],400);	
		}
		
        $verify_code =  $request->verify_code;
        $user = User::where('id', $request->user_id)->first();
		
		if(empty($user))
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User/verify_code is invalid!'
			],400);	
		}
		
        $user = User::where('id',$user_id)->first();
        $google2fa = app('pragmarx.google2fa');
        $secret = $verify_code;
        $valid = $google2fa->verifyKey($user->passwordSecurity->google2fa_secret, $secret);
        if($valid){
            $user->passwordSecurity->google2fa_enable = 1;
            $user->passwordSecurity->save();
            return response()->json([
                'message' => '2FA is Enabled Successfully',
				'user'=>$user
            ],200);
        }else{
            return response()->json([
				'status'=>'error',
                'message' => 'Invalid Verification Code, Please try again.'
            ],400);
        }
    }

    public function disable2fa(Request $request){
		$user_id = $request->user_id;
		$password = $request->password;
		if(empty($user_id) || empty($password))
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User/password is invalid!'
			],400);	
		}
		
        $current_password =  $request->password;
        $user = User::where('id', $request->user_id)->first();
		
		if(empty($user))
		{
			return response()->json([
				'status'=>'error',
				'message'=>'User/password is invalid!'
			],400);	
		}
        if (!(Hash::check($current_password, $user->password))) {
            // The passwords matches
            return response()->json([
				'status'=>'error',
                'message' => 'Your  password does not matches with your account password. Please try again.'
    
            ],400);
        }

        $validatedData = $request->validate([
            'password' => 'required',
        ]);

        //$user = Auth::user();
        $user->passwordSecurity->google2fa_enable = 0;
        $user->passwordSecurity->save();

        return response()->json([
            'message' => '2FA is now Disabled.'
        ],200);
      
    }

    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);

        return Base32::encodeUpper($randomBytes);
    }

    public function canPassWithoutCheckingOTP($user_id)
    {
        $authenticator = app(Google2FAAuthenticator::class);
    }

    public function getGoogle2FASecretKey($user_id)
    {
        $authenticator = app(Google2FAAuthenticator::class);
    }

    public function show2faForm(Request $request){
        $email = "shahin2k5@gmail.com";
        $password = "12345678";
        Auth::attempt(['email' => $email, 'password' => $password]);
        $user = Auth::user();

        $google2fa_url = "";
        if($user->passwordSecurity()->exists()){
            $google2fa = app('pragmarx.google2fa');
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                '5Balloons 2A DEMO',
                $user->email,
                $user->passwordSecurity->google2fa_secret
            );
        }
        $data = array(
            'user' => $user,
            'google2fa_url' => $google2fa_url
        );
        return view('2fa')->with('data', $data);
    }
}
