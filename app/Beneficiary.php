<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    //
    protected $fillable = [
        'first_name', 'last_name', 'user_id','email','mail_address','last_4_beneficiary','mail_address2','city','state','zip'
    ];
}
