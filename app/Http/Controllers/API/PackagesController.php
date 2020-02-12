<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Services\OTPService;
use App\WizardStep;
use App\PackageInfo;
use App\PackageEntity;
use App\UserPackage;
use App\PaymentDetails;
use Illuminate\Support\Facades\Validator;
 

class PackagesController extends Controller
{
    
    public function getPackages(){
        $packages = PackageInfo::all();
        return response()->json([
            'status'=>'success',
            'data'=>$packages
        ]);
    }

    public function savePackageInfo(Request $rs){
        $sub_plan = $rs->sub_plan;
        $user = Auth::user();
        if(!empty($sub_plan)){
            $user_id = $user->id;
            $pkgData = [
                'user_id'=>$user_id,
                'package_id'=>$sub_plan
            ];
            $user_package = new UserPackage;
            $user_pkg = $user_package->saveUserPackage($pkgData);
            $user_pkg->push('package_info',$user_pkg->package_info);
            return response()->json([
                'status'=>'success',
                'package_info'=>$user_pkg->package_info,
                'sub_plan'=>$user_pkg,
            ], 200);
        }
    }

    public function savePaymentInfo(Request $rs){
            $payment_detials = new PaymentDetails;
            $res = $payment_detials->savePaymentDetails($rs);
            if($res){
                return response()->json([
                    'status'=>'success',
                    'package_info'=>$res,
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'package_info'=>$rs->all(),
                ], 400);
            }
           
      
    }

    public function package_info(){
        $package_info = PackageInfo::all();
        $user = Auth::user();
        return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
    }

    public function package_entities($package_id){
        $package_entity = PackageEntity::where('package_id','=',$package_id)->get();
        $user = Auth::user();
        return view('admin.package_entity',['user'=>$user,'package_entity'=>$package_entity]);
    }

    public function user_package(){
        $user = Auth::user();
        $user_package = UserPackage::all();
        return view('admin.user_package',['user'=>$user,'user_package'=>$user_package]);
    }

    // public function setSteps(Request $rs){
    //     $user = Auth::user();
    //     $step = $rs->step;
    //     $info = $rs->info;
    //     $wizStep = WizardStep::where('user_id','=',$user->id)->where('steps',$rs->step)->first();
    //     if(empty($wizStep)){
    //         $wizStep = new WizardStep;
    //     }
    //     $wizStep->user_id = $user->id;
    //     $wizStep->steps = $rs->step;
    //     $wizStep->status = 1;
    //     $wizStep->info = $rs->info;
    //     $wizStep->save();

    //     return response()->json([
    //         'status'=>'success',
    //         'data'=>$wizStep
    //     ]);
    // }
}
