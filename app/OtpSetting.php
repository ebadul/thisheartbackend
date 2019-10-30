<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpSetting extends Model
{
    protected $fillable = ['user_id','otp_method','otp_enable','google_key'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
