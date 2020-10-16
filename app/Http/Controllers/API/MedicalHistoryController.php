<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\DiagnosisInfo;
use App\MedicalHistory;
use App\WizardStep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Auth;

class MedicalHistoryController extends BaseController
{
    public function addDiagnosisName(Request $request)
    {
        //Log::info("Request = ".$request->all);
        $validator = Validator::make($request->all(), [
            'diagnosis_name' => 'required'
        ]);

        $diagnosisCheck = DiagnosisInfo::where("diagnosis_name","=",$request->diagnosis_name)->get();
        //Log::info("diagnosisCheck = ".$diagnosisCheck);

        if(count($diagnosisCheck) == 0){
            $diagnosisInfo = new DiagnosisInfo();

            $diagnosisInfo->diagnosis_name = Crypt::encryptString($request->diagnosis_name);
            $diagnosisInfo->description =Crypt::encryptString( $request->description);
    
            $diagnosisInfo->save();

            return response()->json([
                'message' => 'Diagnosis name added successfully!',
                'diagnosisInfo' => $diagnosisInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Data already exist!',
                'diagnosisInfo' => $diagnosisCheck
            ],400);
        }
 
    }

    public function getPersonTypeDataCountById($user_id)
    {
        // $partnerCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Me")->select('medical_histories.*')->count();
        // Log::info("partnerCount = ".$partnerCount);
        $meCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Me")
        ->select('medical_histories.*')->count();
        $momCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Mom")
        ->select('medical_histories.*')->count();
        $dadCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Dad")
        ->select('medical_histories.*')->count();
        $partnerCount = DB::table('medical_histories')->where('user_id','=',$user_id)->where('member_type','=',"Partner")
        ->select('medical_histories.*')->count();

        return response()->json([
            'meCount' => $meCount,
            'momCount' => $momCount,
            'dadCount' => $dadCount,
            'partnerCount' => $partnerCount,
        ],200);
    }

    public function getAllDiagnosisName()
    {
        $diagnosisInfo = DiagnosisInfo::all();
        foreach( $diagnosisInfo as $diagnosis){
            try{
                $diagnosis->diagnosis_name = Crypt::decryptString($diagnosis->diagnosis_name);
            }catch(\Exception $ex){
                $diagnosis->diagnosis_name = $diagnosis->diagnosis_name;
            }

            try{
                $diagnosis->description = Crypt::decryptString($diagnosis->description);
            }catch(\Exception $ex){
                $diagnosis->description = $diagnosis->description;
            }

             
        }

        return response()->json(['diagnosisInfo' => $diagnosisInfo], 200);
    }

    public function getDiagnosisNameById($id)
    {
        //Get the data
        $diagnosisInfo = DiagnosisInfo::findOrfail($id);
        foreach( $diagnosisInfo as $diagnosis){
            try{
                $diagnosis->diagnosis_name = Crypt::decryptString($diagnosis->diagnosis_name);
            }catch(\Exception $ex){
                $diagnosis->diagnosis_name = $diagnosis->diagnosis_name;
            }

            try{
                $diagnosis->description = Crypt::decryptString($diagnosis->description);
            }catch(\Exception $ex){
                $diagnosis->description = $diagnosis->description;
            }
        }

        return response()->json($diagnosisInfo, 200);
    }

    public function updateDiagnosisById(Request $request, $id)
    {
        $diagnosisInfo = DiagnosisInfo::findOrfail($id);
        if($diagnosisInfo){
            $diagnosisInfo->diagnosis_name = Crypt::encryptString($request->diagnosis_name);
            $diagnosisInfo->description = Crypt::encryptString($request->description);

            $diagnosisInfo->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'diagnosisInfo' => $diagnosisInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Update failed! Data not found for this id.'
            ],401);
        }

    }

    public function deleteDiagnosisById($id)
    {
        //Get the task
        $diagnosisInfo = DiagnosisInfo::findOrfail($id);
 
        if($diagnosisInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'diagnosisInfo' => $diagnosisInfo
            ],200);
        } 
 
    }

    public function saveMedicalHistory(Request $request)
    {
        $diagnosisList = $request->diagnosis_list;
        $addedDiagnosisList = [];
        $diagnosisCSV = "";
        foreach ($diagnosisList as $diagnosisId) {
            //Log::info("diagnosisId = ".$diagnosisId);
            $matchThese = ['user_id' => $request->user_id, 'diagnosis_id' => $diagnosisId, 'member_type' => $request->member_type];
            $dataInfo = MedicalHistory::where($matchThese)->get();

            if(count($dataInfo) == 0){
                $historyInfo = new MedicalHistory();
                $historyInfo->diagnosis_id = $diagnosisId;
                $historyInfo->user_id = $request->user_id;
                $historyInfo->member_type = $request->member_type;
                $historyInfo->save();

                array_push($addedDiagnosisList,$diagnosisId);
            }       
        }
        //Log::info("diagnosisCSV = ".$diagnosisCSV);
        $historyInfoNew = DB::table('medical_histories')->join('diagnosis_infos','diagnosis_id','=','diagnosis_infos.id')
                ->where('user_id','=',$request->user_id)
                ->whereIn('diagnosis_id',$addedDiagnosisList)
                ->where('member_type','=',$request->member_type)
                ->select('medical_histories.id','medical_histories.diagnosis_id','medical_histories.member_type', 'diagnosis_infos.diagnosis_name')->get();
        foreach($historyInfoNew as $new){
            $new->diagnosis_name = Crypt::decryptString($new->diagnosis_name);
        } 
        return response()->json([
            'message' => 'Diagnosis name added successfully!',
            'data' => $historyInfoNew
        ],200);
    }
    
    public function deleteHistoryById($id)
    {
        //Get the task
        $historyInfo = MedicalHistory::findOrfail($id);
 
        if($historyInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'historyInfo' => $historyInfo
            ],200);
        }
    }

    public function getHistoryByUserId($id)
    {
        //Get the data
        //Log::info("Get in");
        $historyInfo = DB::table('medical_histories')->join('diagnosis_infos','diagnosis_id','=','diagnosis_infos.id')
        ->where('user_id','=',$id)->select('medical_histories.id','medical_histories.member_type', 'diagnosis_infos.diagnosis_name')->get();

        return response()->json($historyInfo, 200);
    }

    public function getHistoryByMemberType($type,$id)
    {
        //Get the data
        //Log::info("Id ".$id  ." Type= ".$type);
        $historyInfo = DB::table('medical_histories')->join('diagnosis_infos','diagnosis_id','=','diagnosis_infos.id')
        ->where('user_id','=',$id)->where('member_type','=',$type)->select('medical_histories.id','medical_histories.diagnosis_id','medical_histories.member_type', 'diagnosis_infos.diagnosis_name')->get();
        foreach( $historyInfo as $history){
            try{
                $history->diagnosis_name = Crypt::decryptString($history->diagnosis_name);
            }catch(\Exception $ex){
                $history->diagnosis_name = $history->diagnosis_name;
            }

            // try{
            //     $diagnosis->description = Crypt::decryptString($diagnosis->description);
            // }catch(\Exception $ex){
            //     $diagnosis->description = $diagnosis->description;
            // }

            
        }
        return response()->json($historyInfo, 200);
    }

    public function getAllTypeHistoryByUser($id)
    {
        //Get the data
        //Log::info("Id ".$id  ." Type= ".$type);
        $historyInfo = DB::table('medical_histories')->join('diagnosis_infos','diagnosis_id','=','diagnosis_infos.id')
        ->where('user_id','=',$id)->select('medical_histories.id','medical_histories.diagnosis_id','medical_histories.member_type', 'diagnosis_infos.diagnosis_name')->get();

        return response()->json($historyInfo, 200);
    }

    public function saveHealthOnBoard(Request $rs)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $diagnosis_id = $rs->selectedHealth;
        
        $medical_history = new MedicalHistory;
        $medical_history->user_id = $user_id;
        $medical_history->diagnosis_id = $diagnosis_id;
        $medical_history->member_type = $rs->member_type;
        $medical_history->save();

        $rsStep = (Object) [
            'step' => 'step-06',
            'info' => 'onboardhealth'
        ];
        $wizStep = new WizardStep;
        $wizStep->setSteps($rsStep);

        return response()->json([
            'status'=>'success',
            'data'=>$medical_history], 200);
    }

    public function getDiagnosisInfoAll(){
        $diagnosis_info = new DiagnosisInfo;
        $diagnosis_infos = $diagnosis_info->getDiagnosisInfos();
        return response()->json([
            'status'=>'success',
            'data'=>$diagnosis_infos], 200);
    }

     
    public function diagnosis_info(){
        $diagnosis_infos = DiagnosisInfo::orderBy('diagnosis_name')->get();
        $user = Auth::user();
        return view('admin.diagnosis_infos',['user'=>$user,'diagnosis_info'=>$diagnosis_infos]);
    }

    public function diagnosis_info_edit(Request $rs){
        $user = Auth::user();
        $id = $rs->id;
        $diagnosis_name = Crypt::encryptString($rs->diagnosis_name);
        $description = Crypt::encryptString($rs->description);
    

        $diagnosis_infos = DiagnosisInfo::where('id','=',$id)->first();
        if(!empty($diagnosis_infos)){
            $diagnosis_infos->diagnosis_name = $diagnosis_name;
            $diagnosis_infos->description = $description;
            if($diagnosis_infos->save()){
                return response()->json([
                    'status'=>'success',
                    'diagnosis_infos'=>$diagnosis_infos,
                ], 200);
            }else{
                return response()->json([
                    'status'=>'error',
                    'diagnosis_infos'=>$rs->all(),
                ], 200);
            }
        }
       
        //return view('admin.package_info',['user'=>$user,'package_info'=>$package_info]);
    }

    public function delete_diagnosis_info($diagnosis_id){
        $diagnosis_info = DiagnosisInfo::where('id','=',$diagnosis_id)->first();
        if(!empty($diagnosis_info)){
            if($diagnosis_info->delete()){
                return redirect('/diagnosis_info')->with('success','diagnosis deleted successfully');
            }else{
                return redirect('/diagnosis_info')->with('warning','Sorry, diagnosis not deleted'); 
            }
        }else{
            return redirect('/diagnosis_info')->with('warning','Sorry, diagnosis not deleted');
        }
    
        
    }

    public function diagnosis_info_add(Request $rs){
        $user = Auth::user();
        if($rs->isMethod('post')){
             $diagnosis_info = new DiagnosisInfo;
             $diagnosis_info->diagnosis_name = Crypt::encryptString($rs->diagnosis_name);
             $diagnosis_info->description = Crypt::encryptString($rs->description);
           
             $save_diag_info = $diagnosis_info->save();
             if( $save_diag_info){
                $diagnosis_info = DiagnosisInfo::all();
                //return view('admin.diagnosis_infos',['user'=>$user,'diagnosis_info'=>$diagnosis_info]);
                return redirect('/diagnosis_info');
            }
        }
        
            return view('admin.diagnosis_info_add',['user'=>$user]);
        
    }

   
}
