<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class WizardStep extends Model
{
    public function getSteps(){
        $user = Auth::user();
        $wizStep = $this::where('user_id','=',$user->id)->where('steps',$rs->step)->first();
        return  $wizStep;
    }

    public function setSteps($rs){
        $user = Auth::user();
        $step = $rs->step;
        $info = $rs->info;
        $wizStep = $this::where('user_id','=',$user->id)->where('steps',$rs->step)->first();
        if(empty($wizStep)){
            $wizStep = new WizardStep;
        }
        $wizStep->user_id = $user->id;
        $wizStep->steps = $rs->step;
        $wizStep->status = 1;
        $wizStep->info = $rs->info;
        if($wizStep->save()){
            return true;
        }else{
            return false; 
        }

        
    }
}
