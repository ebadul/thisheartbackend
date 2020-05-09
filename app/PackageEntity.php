<?php

namespace App;
use App\PackageInfo;
use App\PackageEntitiesInfo;
use Illuminate\Database\Eloquent\Model;

class PackageEntity extends Model
{
    //

    public function package_info(){
        return $this->belongsTo(PackageInfo::class,'package_id','id');
    }

    public function entity_info(){
        return $this->belongsTo(PackageEntitiesInfo::class,'package_entities_id');
    }
}
