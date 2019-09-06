<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends BaseController
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function getResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
            return response()->json([
                'message' => 'This email address not exist.'
            ], 404);

        // $rest_user = PasswordReset::where('email', $request->email)->first();
        // if(!$rest_user){
        //     $pass_reset = new PasswordReset();
        //     $pass_reset->email = $user->email;
        //     $pass_reset->token = str_random(60);

        //     $pass_reset->save();
        // }else{
        //     $rest_user->email = $user->email;
        //     $rest_user->token = str_random(60);

        //     $rest_user->save();
        // }
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        $to_name = $user->name;
        $to_email = $user->email;
  
        $data = array(
            'reset_token' => $passwordReset->token,
            'url' => 'http://45.35.50.179:3000/new/password/',
        );
        //Log::info("Before sending... ");    
        Mail::send('emails.reset-request', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('Reset Password Request Mail');
            $message->from('thisheartmailer@gmail.com','This-Heart Mailer');
        });
        //Log::info("After send mail");
        //if ($user && $passwordReset)
            //$user->notify(new PasswordResetRequest($passwordReset->token));

        return response()->json([
            'message' => 'We have e-mailed your password reset link! Please check your email.'
        ], 200);

    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function findToken($token)
    {
        Log::info("Token = ".$token);
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        return response()->json($passwordReset);
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        //Log::info("Email = ".$request->email);
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'token' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user)
            return response()->json([
                'message' => 'Email Id is not valid. Please enter valid email.'
            ], 406);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);

       

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();

        $to_name = $user->name;
        $to_email = $user->email;
        $data = array(
            'url' => '',
        );
        //Log::info("Before sending... ");    
        Mail::send('emails.reset-success', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('Reset Password Success!');
            $message->from('thisheartmailer@gmail.com','This-Heart Mailer');
        });
        //$user->notify(new PasswordResetSuccess($passwordReset));
        
        return response()->json($user);
    }
}