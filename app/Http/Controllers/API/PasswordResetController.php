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

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        //Log::info("Token = ".$passwordReset->token);

        $to_name = $user->name;
        $to_email = $user->email;
        $data = array(
            'url' => 'http://localhost:8000/api/password/find/'.$passwordReset->token,
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

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        $user = User::where('email', $request->email)->first();
        
        if (!$user)
            return response()->json([
                'message' => 'Email address does not exist.'
            ], 404);

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();

        $to_name = $user->name;
        $to_email = "al.mamun74bd@gmail.com";//$user->email;
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