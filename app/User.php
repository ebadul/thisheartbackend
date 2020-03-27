<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;
use Auth;
use App\UserType;
use App\ImageList;
use App\EmailVerification;
use App\UserPackage;
use App\InactiveUserNotify;
use App\PackageEntitiesInfo;
use App\PackageEntity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile','beneficiary_id','user_type'
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
        return $this->hasOne('App\OtpSetting');
    }

    public function OTPCode()
    {
        return $this->hasOne('App\OtpCode');
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

    public function user_types(){
        return $this->belongsTo(UserType::class,'user_type','id');
    }

    public function getUserTypeID($user_type){
        $types = UserType::where('user_type',$user_type)->first();
        return $types->id;
    }

    public function image_list(){
        return $this->hasMany(ImageList::class);
    }

    public function emailVerified(){
        return $this->hasOne(EmailVerification::class);
    }

    public function user_package(){
        return $this->hasOne(UserPackage::class);
    }

    public function user_entities(){
        return $this->hasMany(PackageEntity::class,'package_id',UserPackage::class,'user_id');
    }

    public function inactive_user_notify(){
        return $this->hasOne(InactiveUserNotify::class);
    }
}
