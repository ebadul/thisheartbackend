<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\DiagnosisInfo;
use App\MedicalHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

            $diagnosisInfo->diagnosis_name = $request->diagnosis_name;
            $diagnosisInfo->description = $request->description;
    
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

        return response()->json(['diagnosisInfo' => $diagnosisInfo], 200);
    }

    public function getDiagnosisNameById($id)
    {
        //Get the data
        $diagnosisInfo = DiagnosisInfo::findOrfail($id);

        return response()->json($diagnosisInfo, 200);
    }

    public function updateDiagnosisById(Request $request, $id)
    {
        $diagnosisInfo = DiagnosisInfo::findOrfail($id);
        if($diagnosisInfo){
            $diagnosisInfo->diagnosis_name = $request->diagnosis_name;
            $diagnosisInfo->description = $request->description;

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
        //Below line need when call from postman
        //$diagnosisList = json_decode($request->diagnosis_list);
        
        //Below line need when call from front-end
        $diagnosisList = $request->diagnosis_list;
        //Log::info("Request = ".json_encode($diagnosisList));
        //DB::enableQueryLog();
        $addedDiagnosisList = [];
        $diagnosisCSV = "";
        foreach ($diagnosisList as $diagnosisId) {
            //Log::info("diagnosisId = ".$diagnosisId);
            $matchThese = ['user_id' => $request->user_id, 'diagnosis_id' => $diagnosisId, 'member_type' => $request->member_type];
            $dataInfo = MedicalHistory::where($matchThese)->get();
           // Log::info("dataInfo ".$dataInfo);
            //$query = DB::getQueryLog();
            //Log::info($query);

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

         Log::info("historyInfoNew = ".$historyInfoNew);
         
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
}
