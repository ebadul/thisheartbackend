<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\Beneficiary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BeneficiaryController extends BaseController
{
    public function addBeneficiary(Request $request)
    {
        //Log::info("Request = ".$request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'user_id' => 'required',
            'email' => 'required|email',
            'mail_address' => 'required',
            'mail_address2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'last_4_beneficiary' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Validation error,please check input field.',
            ], 401);

        }else{
            $beneficiaryInfo = new Beneficiary();

            $beneficiaryInfo->first_name = $request->first_name;
            $beneficiaryInfo->last_name = $request->last_name;
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = $request->email;
            $beneficiaryInfo->mail_address = $request->mail_address;
            $beneficiaryInfo->mail_address2 = $request->mail_address2;
            $beneficiaryInfo->city = $request->city;
            $beneficiaryInfo->state = $request->state;
            $beneficiaryInfo->zip = $request->zip;
            $beneficiaryInfo->last_4_beneficiary = $request->last_4_beneficiary;
            $beneficiaryInfo->invite_code = str_random(16);

            $beneficiaryInfo->save();

            return response()->json([
                'message' => 'Beneficiary added successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }

    public function getBeneficiaryById($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $beneficiaryInfo = DB::table('beneficiaries')->where('user_id','=',$user_id)->select('beneficiaries.*')->get();

        return response()->json($beneficiaryInfo, 200);
    }

    public function updateBeneficiaryById(Request $request,$id)
    {
        $beneficiaryInfo = Beneficiary::findOrfail($id);
        if($beneficiaryInfo){
            $beneficiaryInfo->first_name = $request->first_name;
            $beneficiaryInfo->last_name = $request->last_name;
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = $request->email;
            $beneficiaryInfo->mail_address = $request->mail_address;
            $beneficiaryInfo->mail_address2 = $request->mail_address2;
            $beneficiaryInfo->city = $request->city;
            $beneficiaryInfo->state = $request->state;
            $beneficiaryInfo->zip = $request->zip;
            $beneficiaryInfo->last_4_beneficiary = $request->last_4_beneficiary;

            $beneficiaryInfo->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Update failed! Data not found for this id.'
            ],401);
        }

    }

    public function deleteBeneficiaryById($id)
    {
        //Get the task
        $beneficiaryInfo = Beneficiary::findOrfail($id);
 
        if($beneficiaryInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }
}
