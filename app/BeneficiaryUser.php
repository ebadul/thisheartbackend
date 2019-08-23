<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BeneficiaryUser extends Authenticatable
{
    use HasApiTokens;
    //
    protected $fillable = [
        'email', 'password','beneficiary_id','user_id'
    ];
}
