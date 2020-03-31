<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PackageInfo;
use App\User;
use Auth;
use File;

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

    public function package_entities(){
        return $this->hasMany(PackageEntity::class,'package_id','package_id')->with('entity_info');
    }

    
    public function checkPkgEntityActionStop($action_type){
        $user = Auth::user();
        $user_id = $user->id;
        switch($action_type){
            case "images":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Images')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "videos":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Videos')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "records":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Audio Recording')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "storages":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Storage')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $user_path = public_path('uploads/'.$user->id);
                        if (File::exists($user_path)) {
                            $user_storage_size = File::size($user_path);
                            $getAllDirs = File::directories($user_path);
                            $fileSize =[];
                            $totalFileSizeGB=0;
                            foreach( $getAllDirs as $dir ) {
                                $dirNames[] = basename($dir);
                                $fileList=File::files($user_path.'/'.basename($dir));
                                foreach($fileList as $fileTmp){
                                    $fileSize[] = ($fileTmp->getSize()/1024)/1027;
                                    $totalFileSizeGB+=(($fileTmp->getSize()/1024)/1024)/1024;
                                }
                            }

                            if($totalFileSizeGB>=$entity_value){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            default:
                return response()->json([
                    'status'=>'success',
                    'data'=>'yes'
                ]);
        }
    }

    public function saveUserPackage($rs){
        $user_id = $rs['user_id'];
        $package_id =  $rs['package_id'];
        $date = date('Y-m-d');
        $package_info = PackageInfo::where('id','=',$package_id)->orderBy('id','desc')->first();
        $expire_date = date('Y-m-d', strtotime($date.' + '.$package_info->days.' days'));

        $user_pkg = UserPackage::where('user_id','=',$user_id)->first();
        if(empty($user_pkg)){
            $user_pkg = new UserPackage;
        }  
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
