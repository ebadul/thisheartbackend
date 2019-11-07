<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['user_id','user_type','otp_code','verified','expired','expired_time'];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
