<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\PackageInfo;
use App\UserPackage;

class FreeAccount extends Model
{
    //
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function user_pacakge(){
        return $this->belongsTo(UserPackage::class,'user_id','user_id');
    }
}
