<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\User;

class PrimaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function primary_user () {
        $primary_accounts = User:: all ()->toArray();

    
        return view ('admin.primary_user', ['primary_accounts'=>$primary_accounts]);
    }


    public function deleteUserById ($id){
      
        $data = User :: findOrfail ($id);
        if($data -> delete()){
            $user_Id = User :: where ('id', '=', $id)-> first();
        }
        return redirect("/primary_user");
    }


    public function updateUserById(Request $request)
    {

        $data = User::find($request->user_id);

            $data->name = $request->user_name;
            $data->email = $request->email;
            $data ->mobile = $request->mobile;

            $data->save();
        
        return response()->json([
            'status'=>'success',
            'message' => 'Data Updated successfully!',
            'data' => $request->all()
        ],200);
    }


}