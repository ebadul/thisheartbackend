<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;

use App\Letters;
use App\UserPackage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Auth;

class LettersController extends BaseController
{
    
    public function addLetter(Request $request)
    {
        Log::info("Request = ".$request->all);
        // $validator = Validator::make($request->all(), [
        //     'subject' => 'required'
        // ]);
        if($request->subject === ""){
            return response()->json([
                'status'=>'error',
                'message' => 'Subject field could not be empty.'
            ],500);
        }

        if($request->description === ""){
            return response()->json([
                'status'=>'error',
                'message' => 'Description field could not be empty.'
            ],500);
        }

        $user_package = new UserPackage;
        $package_storage_action = $user_package->checkPkgEntityActionStop("letters");
        if($package_storage_action){
            return response()->json([
                'status'=>'error',
                'code'=>'exceeds-letters',
                'message' => "Sorry, your package exceeds the letters saved limit",
                'storage'=>$package_storage_action
            ], 500); 
        }

        $lettersInfo = new Letters();

        $lettersInfo->subject = $request->subject;
        $lettersInfo->description = $request->description;
        $lettersInfo->user_id = $request->user_id;
        $lettersInfo->leter_from = $request->letter_from;

        $lettersInfo->save();

        return response()->json([
            'message' => 'Letter added successfully!',
            'data' => $lettersInfo
        ],200);
    }

    public function getLettersById()
    {
        //Get the data
        $user = Auth::user();
        $user_type = $user->user_types->user_type;
        if(!empty($user_type) && $user_type==="beneficiary"){
            $user_id = $user->beneficiary_id;
        }else{
            $user_id = $user->id;
        }
        $lettersInfo = DB::table('letters')->where('user_id','=',$user_id)->select('letters.*')->get();

        return response()->json($lettersInfo, 200);
    }

    public function updateLetterById(Request $request,$id)
    {
        if($request->subject == ""){
            return response()->json([
                'message' => 'Subject field could not be empty.'
            ],422);
        }

        if($request->description == ""){
            return response()->json([
                'message' => 'Description field could not be empty.'
            ],422);
        }

        $letersInfo = Letters::findOrfail($id);
        if($letersInfo){

            $letersInfo->subject = $request->subject;
            $letersInfo->description = $request->description;

            $letersInfo->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $letersInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Update failed! Data not found for this id.'
            ],401);
        }

    }

    public function deleteLetterById($id)
    {
        //Get the task
        $letterInfo = Letters::findOrfail($id);
 
        if($letterInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $letterInfo
            ],200);
        }
    }

    public function changeStatus(Request $request)
    {
       $letterInfo = Letters::find($request->letter_id)->first();
       $letterInfo->active = 1;
       $letterInfo->save();
  
        return response()->json([
            'message'=>'Status change successfully.',
            'data' =>$letterInfo
        ],200);
    }
}
