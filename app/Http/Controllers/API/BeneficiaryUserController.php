<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\BeneficiaryUser;
use App\User;
use Auth;

class BeneficiaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function beneficiary_user (){
        $beneficiary_accounts =  User::where('user_type','3')-> get ()->toArray();
        $user = Auth::user();
        return view ('admin.beneficiary_user', ['beneficiary_accounts'=>$beneficiary_accounts,'user'=>$user]);
    }

    public function updateBnUserById(Request $request)
    {

            $data = User::where('id','=',$request->user_id)->first(); // data field fillup ! 
            $data->beneficiary_id = $request->beneficiary_id;
            $data->save();
        
        return response()->json([
            'status'=>'success',
            'message' => 'Data Updated successfully!',
            'data' => $request->all()
        ],200);
    }

    public function changeStatus(Request $request)
    {
        $data = User::where('id','=',$request->user_id)->first();
        $data->active = $request->active;
        $data->save();
  
        return response()->json([
            'status'=>'success',
            'success'=>'Status change successfully.'
            ]);
    }
}