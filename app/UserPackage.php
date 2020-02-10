<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PackageInfo;
use App\User;

class UserPackage extends Model
{
    //
    protected $fillable = ['id','user_id','package_id','subscription_date','subscription_expire_date','subscription_status'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function package_info(){
        return $this->belongsTo(PackageInfo::class,'package_id','id');
    }

    public function saveUserPackage($rs){
        $user_id = $rs['user_id'];
        $package_id =  $rs['package_id'];
        $date = date('Y-m-d');
        $package_info = PackageInfo::where('id','=',$package_id)->orderBy('id','desc')->first();
        $expire_date = date('Y-m-d', strtotime($date.' + '.$package_info->days.' days'));
        $user_pkg = new UserPackage;
        $user_pkg->user_id = $user_id;
        $user_pkg->package_id = $package_id;
        $user_pkg->subscription_date = $date;
        $user_pkg->subscription_expire_date = $expire_date;
        $user_pkg->subscription_status = 1;
        if($user_pkg->save()){
            return $user_pkg;
        }else{
            return false;
        }

    }
}
