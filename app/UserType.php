<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\OtpSetting;

class UserType extends Model
{
    protected $fillable = ['user_type'];
    
    public function users(){
        return $this->hasMany(User::class);
    }

    public function otp_setting(){
        return $this->hasMany(OtpSetting::class);
    }
}
