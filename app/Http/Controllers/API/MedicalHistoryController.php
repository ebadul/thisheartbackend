<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\DiagnosisInfo;
use App\MedicalHistory;
use Illuminate\Support\Facades\Log;

class MedicalHistoryController extends BaseController
{
    public function addDiagnosisName(Request $request)
    {
        Log::info("Request = ".$request->all);
        $validator = Validator::make($request->all(), [
            'diagnosis_name' => 'required'
        ]);

        $diagnosisInfo = new DiagnosisInfo();

        $diagnosisInfo->diagnosis_name = $request->diagnosis_name;
        $diagnosisInfo->description = $request->description;

        $diagnosisInfo->save();

        return response()->json([
            'message' => 'Diagnosis name added successfully!',
            'diagnosisInfo' => $diagnosisInfo
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

    public function addDiagnosisNameForPartner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'history_list' => 'required',
            'user_id' => 'required',
            'member_type' => 'required'
        ]);
        $historyList = json_decode($request->history_list);
        //Log::info("Request = ".$historyList->name);
        foreach ($historyList as $historyId) {
            $historyInfo = new MedicalHistory();
            $historyInfo->diagnosis_id = $historyId;
            $historyInfo->user_id = $request->user_id;
            $historyInfo->member_type = $request->member_type;
            $historyInfo->save();
            //Log::info("Request = ".$historyId);
        }

        return response()->json([
            'message' => 'Diagnosis name added successfully!'
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
        $historyInfo = MedicalHistory::where('user_id','=',$id)->get();

        return response()->json($diagnosisInfo, 200);
    }

}
