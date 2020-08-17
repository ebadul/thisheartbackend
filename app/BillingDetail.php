<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function user_package(){
        return $this->hasOne(User::class, UserPackage::class, "user_id");
    }

    public function package_info(){
        return $this->belongsTo(PackageInfo::class, "package_id");
    }
}
