<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Services\OTPService;
use App\WizardStep;
use App\LifeStyle;
use App\UserLifeStyle;
use Illuminate\Support\Facades\Validator;

class LifeStyleController extends Controller
{
    public function getLifeStyle(){
        $lifeStyle = LifeStyle::get();
        return response()->json([
            'status'=>'success',
            'data'=>$lifeStyle
        ]);
    }

    public function setLifeStyle(Request $rs){
        $user = Auth::user();
        $lifeStyle = UserLifeStyle::where('user_id','=',$user->id)->first();
        if(empty( $lifeStyle)){
            $lifeStyle = new UserLifeStyle;
        }

        $lifeStyle->user_id = $user->id;
        $lifeStyle->life_style_id = $rs->lifeStyle;
        $lifeStyle->save();

        $step = "step-04";
        $info = "life style";
        $wizStep = WizardStep::where('user_id','=',$user->id)->where('steps',$step)->first();
        if(empty($wizStep)){
            $wizStep = new WizardStep;
        }
        $wizStep->user_id = $user->id;
        $wizStep->steps = $step;
        $wizStep->status = 1;
        $wizStep->info = $info;
        $wizStep->save();


        return response()->json([
            'status'=>'success',
            'data'=>$lifeStyle
        ]);
    }
}
