<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Memories;
use App\ImageList;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use File;
use Auth;

class MemoriesController extends BaseController
{

    public function storeImage(Request $request)
    {
        Log::info("Title = ".$request->title);
        //Log::info("Image File = ".$request->file('image'));

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $imageName = str_random(60);
           
            $name = $imageName.'.'.$image->extension();
            $path_str = 'uploads/images/'.$request->user_id;
            
            $path = $image->storeAs($path_str,$name);
 
            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $path;
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
            'data' => $memories
        ], 200);
    }

    public function storeProfileImage(Request $request)
    {
        $user_id = Auth::user()->id;
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = str_random(60);
           
            $name = $imageName.'.'.$image->extension();
            $path_str = 'uploads/profile-images/'.$user_id;
            
            $path = $image->storeAs($path_str,$name);
 
            $imageList = ImageList::firstOrNew(['user_id'=>$user_id]);
            $imageList->user_id = $user_id;
            $imageList->image_type = "profile";
            $imageList->image_url = $path;
            $imageList->status = 1;
            $imageList->save();

          }
          else{
            return response()->json([
                'message' => 'Please select image file.',
            ], 401);
          }
    

        return response()->json([
            'status'=>'success',
            'message' => 'Image uploaded successfully.',
            'data' => $imageList
        ], 200);
    }

    public function getContentDataCountById($user_id)
    {
        $imageCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"image")
        ->select('memories.*')->count();
        $videoCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"video")
        ->select('memories.*')->count();
        $recordCount = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"record")
        ->select('memories.*')->count();
        $letterCount = DB::table('letters')->where('user_id','=',$user_id)->select('letters.*')->count();

        return response()->json([
            'imageCount' => $imageCount,
            'videoCount' => $videoCount,
            'recordCount' => $recordCount,
            'letterCount' => $letterCount,
        ],200);
    }

    public function getAllImagesById(Request $rs, $user_id)
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
        $image_path = storage_path()."uploads/images/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        //Log::info("file path = ".$image_path);
        if (File::exists($image_path)) {
            Log::info("file exist");
            File::delete($image_path);
        }

        if($memoriesInfo->delete()) {

            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo
            ],200);
        }
    }

    public function storeVideo(Request $request)
    {
        $max_size = (int)ini_get('upload_max_filesize') * 1000;
        Log::info("max_size = ".$max_size);
        //Log::info("Image File = ".$request->file('image'));|max:10000040
        $data=$request->all();
        $rules=['video' =>'mimes:mpeg,ogg,ogv,mp4,webm,3gp,mov,flv,avi,wmv,ts|max:'.$max_size.'|required'];
        $validator = Validator($data,$rules);
        
        if ($validator->fails()){
            return response()->json([
                'message' => 'Please select valid video file.',
            ], 400);

        }else{
            $video = $request->file('video');
            $videoName = str_random(60);
            $name = $videoName.'.'.$video->getClientOriginalExtension();
            $path_str = 'uploads/videos/'.$request->user_id;
            $path = $video->storeAs($path_str,$name);

            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $path;
            $memories->filetype = "video";
            $memories->user_id = $request->user_id;

            $memories->save();

            return response()->json([
                'message' => 'Video uploaded successfully.',
                'data' => $memories
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
        $imagesInfo = DB::table('memories')->whereDate('created_at', Carbon::now()->subDays($day))->where('filetype','=',"video")->where('user_id','=',$user_id)->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function deleteVideoById($id)
    {
        //Get the task
        $memoriesInfo = Memories::findOrfail($id);
        //Delete file from disk.
        $video_path = storage_path()."uploads/videos/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        if (File::exists($video_path)) {
            Log::info("file path = ".$video_path);
            File::delete($video_path);
        }

        if($memoriesInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo
            ],200);
        }
    }

    public function storeAudioRecord(Request $request)
    {
        $max_size = (int)ini_get('upload_max_filesize') * 1000;
        //Log::info("max_size = ".$max_size);
        //Log::info("Image File = ".$request->file('image'));|max:10000040
        $data=$request->all();
        $rules=['audio' =>'mimes:mpeg,mpga,mp3,m4a,wma,webM,wav,ogg,aac|max:'.$max_size.'|required'];
        $validator = Validator($data,$rules);
        
        if ($validator->fails()){
            return response()->json([
                'message' => 'Please select valid audio file.',
            ], 401);

        }else{
            $audio = $request->file('audio');
            $audioName = str_random(60);
           
            $name = $audioName.'.'.$audio->getClientOriginalExtension();
            $path_str = 'uploads/audios/'.$request->user_id;
            $path = $audio->storeAs($path_str,$name);

            $memories = new Memories();
            $memories->title = $request->title;
            $memories->filename = $path;
            $memories->filetype = "record";
            $memories->user_id = $request->user_id;

            $memories->save();

            return response()->json([
                'message' => 'Audio uploaded successfully.',
                'data' => $memories
            ], 200);
        }

    }

    public function getAllAudioRecordById($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"record")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function getRecentAudioRecordByDay($user_id,$day)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $imagesInfo = DB::table('memories')->whereDate('created_at', Carbon::now()->subDays($day))->where('filetype','=',"record")->where('user_id','=',$user_id)->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function deleteAudioRecordById($id)
    {
        //Get the task
        $memoriesInfo = Memories::findOrfail($id);
        //Delete file from disk.
        $video_path = storage_path()."uploads/audios/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        if (File::exists($video_path)) {
            Log::info("file path = ".$video_path);
            File::delete($video_path);
        }

        if($memoriesInfo->delete()) {
            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo
            ],200);
        }
    }
   
}