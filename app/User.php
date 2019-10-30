<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','country_code','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function passwordSecurity()
    {
        return $this->hasOne('App\PasswordSecurity');
    }

    public function TwoFaSmsSetting()
    {
        return $this->hasOne('App\TwoFaSmsSetting');
    }

    public function OTPSetting()
    {
        return $this->hasOne('App\OTPSetting');
    }

    public function OTPCode()
    {
        return $this->hasOne('App\OTPCode');
    }

    public function TwoFaOtp()
    {
        return $this->hasOne('App\TwoFaOtp');
    }

    public function checkPasswordOTP($request){
        $password = $request->pass_word;
        $isCorrectPassword = Hash::check($password,$this->password);
        return $isCorrectPassword;
    }
}
