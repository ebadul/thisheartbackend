<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use User;

class EmailVerification extends Model
{
    protected $fillable = ['user_id','verified_token','email_verified'];
   
    public function user(){
        return $this->belongsTo(User::class);
    }
}
