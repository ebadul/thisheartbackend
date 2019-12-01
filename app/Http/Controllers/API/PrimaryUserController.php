<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use Auth;

class PrimaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginAdmin (Request $request) {
        return view('admin.login');
    }

    public function adminLogout (Request $request) {
        Auth::logout();
        return view('admin.login');
    }

    public function adminUser (Request $request) {
        if(Auth::check()){
            return redirect('/dashboard');
        }else{
            return redirect('login');
        }
    }

    
    public function dashboard (Request $request) {
        if(Auth::check()){
            $user= Auth::user();
            $primary_accounts = User:: where('user_type','2')-> count () ;
            $beneficiary_accounts = User:: where('user_type','3')-> count () ;
            return view('admin.dashboard')->with(['user'=>$user,'primary_users'=>$primary_accounts ,'beneficiary_count'=>$beneficiary_accounts]);
        }else{
            return redirect('login');
        }
    }

    public function primary_user_login (Request $request) {
         
        $validate = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        
        if($validate->fails()){
            return redirect('/login')->withErrors([
                'message'=>"User or password doesn't match"
            ]);
        }
        $userTmp = new User;
        $user_type_id = $userTmp->getUserTypeID('admin');
        try{
               
             if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password,'user_type'=>$user_type_id]) ){
                $user = Auth::user();
                return redirect('/dashboard');
             }else{
                Auth::logout();
                return redirect('/login')->withErrors([
                    'message'=>"User or password doesn't match"
                ]);
             }
           
        }catch(Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        
    }

    public function primary_user () {
        $primary_accounts = User:: where('user_type','2')-> get ()->toArray();
        $user = Auth::user();
        return view ('admin.primary_user', ['primary_accounts'=>$primary_accounts,'user'=>$user]);
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
    public function changeStatus(Request $request)
    {
        $data = User::find($request->user_id);
        $data->active = $request->active;
        $data->save();
  
        return response()->json(['success'=>'Status change successfully.']);
    }
}