<?php

namespace App\Http\Controllers\API;

use App\User;
use App\PasswordSecurity;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Support\Google2FAAuthenticator;
use Illuminate\Support\Facades\Log;
use Crypt;
use PragmaRX\Google2FA\Google2FA;

class PasswordSecurityController extends BaseController
{
    //
    public function getQRCode($user_id){

        // //generate new secret
        // $secret = $this->generateSecret();

        // //get user
        $user = User::where('id', '=', $user_id)->first();

        // $google2fa_url = "";
        // if($user->passwordSecurity()->exists() == false){
        //     //encrypt and then save secret
        //     $user->passwordSecurity->google2fa_secret = Crypt::encrypt($secret);
        //     $user->save();
        // }else{

        //     $google2fa = app('pragmarx.google2fa');
        //     $imageDataUri = Google2FA::getQRCodeInline(
        //         'thisheart_app',
        //         $user->email,
        //         $secret,
        //         200
        //     );
        //     $google2fa_url = $google2fa->getQRCodeGoogleUrl(
        //         '5Balloons 2A DEMO',
        //         $user->email,
        //         $user->passwordSecurity->google2fa_secret
        //     );
        // }
        // $data = array(
        //     'user' => $user,
        //     'google2fa_url' => $google2fa_url
        // );

        $google2fa_url = "";
        //if($user->passwordSecurity()->exists()){
            //$google2fa = app('pragmarx.google2fa');
            $google2fa_url = Google2FA::getQRCodeGoogleUrl(
                '5Balloons 2A DEMO',
                $user->email,
                $user->passwordSecurity->google2fa_secret
            );
        //}
       
        return response()->json([
            'message' => 'User varified successfully!',
            'user' => $user,
            'google2fa_url' => $google2fa_url

        ],200);
    }

    public function generate2faSecret(Request $request){
        $user = User::where('id', '=', $request->user_id)->first();
        //Log::info("User Info... ". $user );
        // Initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');
    
        // Add the secret key to the registration data
        $passSecInfo = PasswordSecurity::where('user_id', '=', $request->user_id);
        Log::info("PassSecInfo... ". $passSecInfo );
        if(!$passSecInfo){
            Log::info("Into passSecInfo... ". $passSecInfo );
            $passSecInfo = PasswordSecurity::create([
                'user_id' => $user->id,
                'google2fa_enable' => 0,
                'google2fa_secret' => $google2fa->generateSecretKey(),
            ]);
        }
            
        return response()->json([
            'message' => 'Secret Key is generated, Please verify Code to Enable 2FA!',
            'password_security' => $passSecInfo

        ],200);
        //return redirect('/2fa')->with('success',"Secret Key is generated, Please verify Code to Enable 2FA");
    }

    public function enable2fa(Request $request){
        $user = User::where('id', '=', $request->user_id)->first();
        //$user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $secret = $request->verify_code;

        $valid = $google2fa->verifyKey($user->passwordSecurity->google2fa_secret, $secret);
        
        if($valid){
            $user->passwordSecurity->google2fa_enable = 1;
            $user->passwordSecurity->save();

            return response()->json([
                'message' => '2FA is Enabled Successfully'
    
            ],200);
            //return redirect('2fa')->with('success',"2FA is Enabled Successfully.");
        }else{
            return response()->json([
                'message' => 'Invalid Verification Code, Please try again.'
    
            ],400);
            //return redirect('2fa')->with('error',"Invalid Verification Code, Please try again.");
        }
    }

    public function disable2fa(Request $request){
        $current_password =  $request->current_password;
        $user = User::where('id', '=', $request->user_id)->first();

        if (!(Hash::check($current_password, $user->password))) {
            // The passwords matches
            return response()->json([
                'message' => 'Your  password does not matches with your account password. Please try again.'
    
            ],400);
            //return redirect()->back()->with("error","Your  password does not matches with your account password. Please try again.");
        }

        $validatedData = $request->validate([
            'current_password' => 'required',
        ]);

        //$user = Auth::user();
        $user->passwordSecurity->google2fa_enable = 0;
        $user->passwordSecurity->save();

        return response()->json([
            'message' => '2FA is now Disabled.'

        ],200);
        //return redirect('/2fa')->with('success',"2FA is now Disabled.");
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
}
