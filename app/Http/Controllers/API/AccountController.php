<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Account;
use Validator;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Auth;
use Hash;

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

            $accountInfo->acc_type = Crypt::encryptString($request->acc_type);
            $accountInfo->acc_name = Crypt::encryptString($request->acc_name);
            $accountInfo->user_id = $request->user_id;
            $accountInfo->acc_url = Crypt::encryptString($request->acc_url);
            $accountInfo->acc_description = Crypt::encryptString($request->acc_description);

            $accountInfo->acc_user_name = Crypt::encryptString($request->acc_user_name);
            $accountInfo->acc_password = Crypt::encryptString($request->acc_password);

            $accountInfo->save();
          
            
                $accountInfo->acc_type = Crypt::decryptString($accountInfo->acc_type);
                $accountInfo->acc_name = Crypt::decryptString($accountInfo->acc_name);
                $accountInfo->acc_url = Crypt::decryptString($accountInfo->acc_url);
                $accountInfo->acc_description = Crypt::decryptString($accountInfo->acc_description);
                $accountInfo->acc_user_name = Crypt::decryptString($accountInfo->acc_user_name);
                $accountInfo->acc_password = Crypt::decryptString($accountInfo->acc_password);
            
            return response()->json([
                'message' => 'Accounts data added successfully!',
                'data' => $accountInfo
            ],200);
        }
    }

    public function getAccountByUserId()
    {
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        $user_type_id = $user->user_types->id;
        if($user_type_id===3){
            $user_id = $user->beneficiary_id;
        }else{
            $user_id=$user->id;
        }
        $accountInfo = DB::table('accounts')->where('user_id','=',$user_id)->select('accounts.*')->get();

        foreach($accountInfo as $value){
            $value->acc_type = Crypt::decryptString($value->acc_type);
            $value->acc_name = Crypt::decryptString($value->acc_name);
            $value->acc_url = Crypt::decryptString($value->acc_url);
            $value->acc_description = Crypt::decryptString($value->acc_description);
            $value->acc_user_name = Crypt::decryptString($value->acc_user_name);
            $value->acc_password = Crypt::decryptString($value->acc_password);
        }

        return response()->json($accountInfo, 200);
    }

    public function getAccountInfo(Request $rs)
    {
         
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }
        $user_type_id = $user->user_types->id;
        if($user_type_id===3){
            $user_id = $user->beneficiary_id;
        }else{
            $user_id=$user->id;
        }
        $accountInfo = DB::table('accounts')->where('user_id','=',$user_id)->select('accounts.*')->get();

        foreach($accountInfo as $value){
            $value->acc_type = Crypt::decryptString($value->acc_type);
            $value->acc_name = Crypt::decryptString($value->acc_name);
            $value->acc_url = Crypt::decryptString($value->acc_url);
            $value->acc_description = Crypt::decryptString($value->acc_description);
            $value->acc_user_name = Crypt::decryptString($value->acc_user_name);
            $value->acc_password = Crypt::decryptString($value->acc_password);
        }

        return response()->json($accountInfo, 200);
    }


    public function updateAccountInfo(Request $rs)
    {
         
        $user = Auth::user();
        if(empty($user)){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found!',
            ]);
        }

        $current_pass = $rs->currentPass;
        $new_pass = $rs->newPass;
        $sub_plan = $rs->sub_plan;
        $checkPass = Hash::check($current_pass,$user->password);
        if($checkPass){
            !empty($new_pass)?$user->password = bcrypt($new_pass):'';
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>"Current password doesn't matched!",
            ]);
        }
        $user->name = Crypt::encryptString($rs->user_name);
        !empty($rs->mobile)?$user->mobile = $rs->mobile:'';
        if($user->save()){
            return response()->json([
                'status'=>'success',
                'user_name'=>Crypt::decryptString($user->name),
                'data'=>$user,
            ], 200);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>"Update didn't finish successfully!",
            ]);
        }
        
    }

    public function updateAccountById(Request $request,$id)
    {
        $accountInfo = Account::findOrfail($id);

        if($accountInfo){
            $accountInfo->acc_type = Crypt::encryptString($request->acc_type);
            $accountInfo->acc_name = Crypt::encryptString($request->acc_name);
            $accountInfo->user_id = $request->user_id;
            $accountInfo->acc_url = Crypt::encryptString($request->acc_url);
            $accountInfo->acc_description = Crypt::encryptString($request->acc_description);
            $accountInfo->acc_user_name = Crypt::encryptString($request->acc_user_name);
            $accountInfo->acc_password = Crypt::encryptString($request->acc_password);

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