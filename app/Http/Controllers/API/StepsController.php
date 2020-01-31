<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Services\OTPService;
use App\WizardStep;
use App\LifeStyle;
use Illuminate\Support\Facades\Validator;

class StepsController extends Controller
{
    
    public function getSteps(Request $rs){
        return response()->json([
            'status'=>'success',
            'data'=>$rs->all()
        ]);
    }

    public function setSteps(Request $rs){
        $user = Auth::user();
        $step = $rs->step;
        $info = $rs->info;
        $wizStep = WizardStep::where('user_id','=',$user->id)->where('steps',$rs->step)->first();
        if(empty($wizStep)){
            $wizStep = new WizardStep;
        }
        $wizStep->user_id = $user->id;
        $wizStep->steps = $rs->step;
        $wizStep->status = 1;
        $wizStep->info = $rs->info;
        $wizStep->save();

        return response()->json([
            'status'=>'success',
            'data'=>$wizStep
        ]);
    }
}
