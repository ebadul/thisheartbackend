<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Memories;
use Validator;
use Illuminate\Support\Facades\Log;

class MemoriesController extends BaseController
{

    public function storeImage(Request $request)
    {
        //Log::info("Title = ".$request->title);
        //Log::info("Image File = ".$request->file('image'));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = str_random(60);
           
            $name = $imageName.'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/'.$request->userid);
            $imagePath = $destinationPath. "/".  $name;
            $image->move($destinationPath, $name);

            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $name;
            $memories->filetype = "image";
            $memories->user_id = $request->userid;

            $memories->save();
          }
          else{
            return response()->json([
                'message' => 'Please select image file.',
            ], 401);
          }
    

        return response()->json([
            'message' => 'Image uploaded successfully.',
        ], 200);
    }


   
}