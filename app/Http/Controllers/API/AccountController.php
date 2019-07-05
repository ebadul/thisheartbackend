<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Account;
use Validator;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AccountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAccount(Request $request)
    {
        //Log::info("Request = ".$request->all());
        $validator = Validator::make($request->all(), [
            'acc_type' => 'required',
            'acc_name' => 'required',
            'user_id' => 'required',
            'acc_url' => 'required',
            'acc_description' => 'required',
            'acc_user_name' => 'required',
            'acc_password' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Validation error,please check reequired input field.',
            ], 400);

        }else{
            $accountInfo = new Account();

            $accountInfo->acc_type = $request->acc_type;
            $accountInfo->acc_name = $request->acc_name;
            $accountInfo->user_id = $request->user_id;
            $accountInfo->acc_url = $request->acc_url;
            $accountInfo->acc_description = $request->acc_description;
            $accountInfo->acc_user_name = $request->acc_user_name;
            $accountInfo->acc_password = $request->acc_password;

            $accountInfo->save();
           
            return response()->json([
                'message' => 'Accounts data added successfully!',
                'data' => $accountInfo
            ],200);
        }
    }

    public function getAccountByUserId($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $accountInfo = DB::table('accounts')->where('user_id','=',$user_id)->select('accounts.*')->get();

        return response()->json($accountInfo, 200);
    }

    public function updateAccountById(Request $request,$id)
    {
        $accountInfo = Account::findOrfail($id);

        if($accountInfo){
            $accountInfo->acc_type = $request->acc_type;
            $accountInfo->acc_name = $request->acc_name;
            $accountInfo->user_id = $request->user_id;
            $accountInfo->acc_url = $request->acc_url;
            $accountInfo->acc_description = $request->acc_description;
            $accountInfo->acc_user_name = $request->acc_user_name;
            $accountInfo->acc_password = $request->acc_password;

            $accountInfo->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $accountInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Update failed! Data not found for this id.'
            ],404);
        }

    }

    public function deleteAccountById($id)
    {
        //Get the task
        $accountInfo = Account::findOrfail($id);
 
        if($accountInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $accountInfo
            ],200);
        }
    }
}