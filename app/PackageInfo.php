<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UserPackage;

class PackageInfo extends Model
{
    //
    public function user_package(){
        return $this->hasMany(UserPackage::class);
    }
}
