<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $fillable = [
        'acc_type', 'acc_name', 'user_id','acc_url','acc_description','acc_user_name','acc_password'
    ];
}
