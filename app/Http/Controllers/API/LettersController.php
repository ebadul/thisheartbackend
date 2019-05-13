<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;

use App\Letters;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LettersController extends BaseController
{
    
    public function addLetter(Request $request)
    {
        Log::info("Request = ".$request->all);
        // $validator = Validator::make($request->all(), [
        //     'subject' => 'required'
        // ]);

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

    public function getLettersById($user_id)
    {
        Log::info("user_id = ".$user_id);
        //Get the data
        $lettersInfo = DB::table('letters')->where('user_id','=',$user_id)->select('letters.*')->get();
        
        //$lettersInfo = Letters::where("user_id" , $user_id);

        return response()->json($lettersInfo, 200);
    }

    public function updateLetterById(Request $request,$id)
    {
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
}
