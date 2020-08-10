<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserBilling extends Model
{
    //

    public function user(){
        return $this->belongsTo(User::class);
    }


    
    public function user_package(){
        return $this->hasOne(User::class, UserPackage::class, "user_id");
    }
    

}
