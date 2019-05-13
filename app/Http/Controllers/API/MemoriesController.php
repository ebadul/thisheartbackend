<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Memories;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use File;

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
            $destinationPath = public_path('/uploads/'.$request->user_id);
            $imagePath = $destinationPath. "/".  $name;
            $image->move($destinationPath, $name);

            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $name;
            $memories->filetype = "image";
            $memories->user_id = $request->user_id;

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

    public function getAllImagesById($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"image")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function getRecentImagesByDay($user_id,$day)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->whereDate('created_at', Carbon::now()->subDays($day))->where('filetype','=',"image")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function deleteImageById($id)
    {
        //Get the task
        $memoriesInfo = Memories::findOrfail($id);

        //Delete file from disk.
        $image_path = public_path()."/uploads/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        
        if (File::exists($image_path)) {
            Log::info("file path = ".$image_path);
            File::delete($image_path);
        }

        if($memoriesInfo->delete()) {

            return response()->json([
                'message' => 'Data deleted successfully!',
                'memoriesInfo' => $memoriesInfo
            ],200);
        }
    }

    public function storeVideo(Request $request)
    {
        $max_size = (int)ini_get('upload_max_filesize') * 1000;
        Log::info("max_size = ".$max_size);
        //Log::info("Image File = ".$request->file('image'));|max:10000040
        $data=$request->all();
        $rules=['video' =>'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts|max:'.$max_size.'|required'];
        $validator = Validator($data,$rules);
        
        if ($validator->fails()){
            return response()->json([
                'message' => 'Please select valid video file.',
            ], 401);

        }else{
            $video = $request->file('video');
            $videoName = str_random(60);
           
            $name = $videoName.'.'.$video->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/video/'.$request->user_id);
            $imagePath = $destinationPath. "/".  $name;
            $video->move($destinationPath, $name);

            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $name;
            $memories->filetype = "video";
            $memories->user_id = $request->user_id;

            $memories->save();

            return response()->json([
                'message' => 'Video uploaded successfully.',
            ], 200);
        }

    }

    public function getAllVideoById($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"video")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function getRecentVideoByDay($user_id,$day)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->whereDate('created_at', Carbon::now()->subDays($day))->where('filetype','=',"video")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function deleteVideoById($id)
    {
        //Get the task
        $memoriesInfo = Memories::findOrfail($id);
        //Delete file from disk.
        $video_path = public_path()."/uploads/video/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        if (File::exists($video_path)) {
            Log::info("file path = ".$video_path);
            File::delete($video_path);
        }

        if($memoriesInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'memoriesInfo' => $memoriesInfo
            ],200);
        }
    }
   
}