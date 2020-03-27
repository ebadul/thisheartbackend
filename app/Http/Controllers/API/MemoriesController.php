<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Memories;
use App\ImageList;
use App\SocialPhotos;
use App\UserPackage;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use File;
use Storage;
use Auth;

class MemoriesController extends BaseController
{

    public function storeImage(Request $request)
    {
        $user = Auth::user();
        $tmpMemories =[];
        
        if ($request->hasFile('imagesFiles')) {
            $user_package = new UserPackage;
            $package_count_action = $user_package->checkPkgEntityActionStop("images");
           
            if($package_count_action){
                return response()->json([
                    'message' => "Sorry, your package exceeds the store more images",
                ], 500); 
            }

            $package_storage_action = $user_package->checkPkgEntityActionStop("storages");
            if($package_storage_action){
                return response()->json([
                    'message' => "Sorry, your package exceeds the storage limit",
                    'storage'=>$package_storage_action
                ], 500); 
            }

            $imageFile = $request->file('imagesFiles');
            foreach($imageFile as $image){
                $imageName = str_random(60);
           
                $name = $imageName.'.'.$image->extension();
                $path_str = 'uploads/'.$user->id.'/images';
                
                $path = $image->storeAs($path_str,$name);
                $file_name = $image->getClientOriginalName();
                $title = pathinfo($file_name, PATHINFO_FILENAME);;
                $memories = new Memories();
                $memories->title = $title;
                $memories->filename = $path;
                $memories->filetype = "image";
                $memories->user_id = $user->id;
    
                $memories->save();
                $tmpMemories[]=  $memories;
            }
           
          }
          else{
            return response()->json([
                'message' => 'Please select image file.',
            ], 401);
          }
    

        return response()->json([
            'message' => 'Image uploaded successfully.',
            'data' => $tmpMemories
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

    public function getAllImagesById(Request $rs)
    {
         //Get the data
         $user = Auth::user();
         $user_type = $user->user_types->user_type;
         if(!empty($user_type) && $user_type==="beneficiary"){
             $user_id = $user->beneficiary_id;
         }else{
             $user_id = $user->id;
         }
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
        // $image_path = storage_path()."uploads/images/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        $image_path = public_path('/').$memoriesInfo->filename;
        //Log::info("file path = ".$image_path);
        if (File::exists($image_path)) {
            Log::info("file exist");
            File::delete($image_path);
            
        }

        if($memoriesInfo->delete()) {

            return response()->json([
                'status'=>'success',
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo,
                'filepath'=>$image_path
            ],200);
        }
    }
    public function deleteSocialImageById($id)
    {
        //Get the task
        $socialPhoto = SocialPhotos::where('id', $id)->delete();

        if($socialPhoto) {

            return response()->json([
                'status' => 'success',
                'message' => 'Data deleted successfully!',
                'data'=>$id
            ],200);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Data delete fail!',
                'data'=>$id
            ],200);
        }

    }

    public function storeVideo(Request $request)
    {
        $max_size = (int)ini_get('upload_max_filesize') * 1000000;
        $user = Auth::user();
  
            if($request->hasFile('videos')){
                $user_package = new UserPackage;
                $package_action = $user_package->checkPkgEntityActionStop("videos");
                if($package_action){
                    return response()->json([
                        'message' => "Sorry, your package exceeds the store more video",
                    ], 500); 
                }

                $package_storage_action = $user_package->checkPkgEntityActionStop("storages");
                if($package_storage_action){
                    return response()->json([
                        'message' => "Sorry, your package exceeds the storage limit",
                        'storage'=>$package_storage_action
                    ], 500); 
                }

                $videos = $request->file('videos');
                $memoriesTmp = [];
                foreach($videos as $video){
                    $videoName = str_random(60);
                    $name = $videoName.'.'.$video->getClientOriginalExtension();
                    $path_str = 'uploads/'.$user->id.'/videos';
                    $path = $video->storeAs($path_str,$name);
                    $file_name = $video->getClientOriginalName();
                    $title = pathinfo($file_name, PATHINFO_FILENAME);;
                    $memories = new Memories();
                    $memories->title = $title;
                    //$memories->filename = $path_str.'/'.$name; //filename and full path
                    $memories->filename = $path;
                    $memories->filetype = "video";
                    $memories->user_id = $user->id;
                    $memories->save();
                    $memoriesTmp[] = $memories;
                }

                
                return response()->json([
                    'message' => 'Video uploaded successfully.',
                    'data' => $memoriesTmp
                ], 200);
                    
            } else{
                return response()->json([
                    'message' => 'Please select video file.',
                ], 401);
              }

    }

    public function getAllMemoriesData()
    {
         //Get all the data
         $user = Auth::user();
         $user_type = $user->user_types->user_type;
         if(!empty($user_type) && $user_type==="beneficiary"){
             $user_id = $user->beneficiary_id;
         }else{
             $user_id = $user->id;
         }
        $memoriesInfo = DB::table('memories')->where('user_id','=',$user_id)
        ->select('memories.*')->get();

        return response()->json($memoriesInfo, 200);
    }

    public function getAllVideoById()
    {
         //Get the data
         $user = Auth::user();
         $user_type = $user->user_types->user_type;
         if(!empty($user_type) && $user_type==="beneficiary"){
             $user_id = $user->beneficiary_id;
         }else{
             $user_id = $user->id;
         }
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
        $video_path = public_path('/').$memoriesInfo->filename;
        if (File::exists($video_path)) {
            Log::info("file path = ".$video_path);
            File::delete($video_path);
            
        }

        if($memoriesInfo->delete()) {
            return response()->json([
                'status'=>'success',
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo
            ],200);
        }
    }

    public function storeAudioRecord(Request $request)
    {
        $max_size = (int)ini_get('upload_max_filesize') * 100000;
        $user = Auth::user();

            if($request->hasFile('audios')){

                $user_package = new UserPackage;
                $package_action = $user_package->checkPkgEntityActionStop("records");
                if($package_action){
                    return response()->json([
                        'message' => "Sorry, your package exceeds the store more record",
                    ], 500); 
                }

                $package_storage_action = $user_package->checkPkgEntityActionStop("storages");
                if($package_storage_action){
                    return response()->json([
                        'message' => "Sorry, your package exceeds the storage limit",
                        'storage'=>$package_storage_action
                    ], 500); 
                }

                $audios = $request->file('audios');
                $memoriesTmp = [];
                foreach($audios as $audio){
                    $audioName = str_random(60);
                
                    $name = $audioName.'.'.$audio->getClientOriginalExtension();
                    $path_str = 'uploads/'.$user->id.'/audios';
                    $path = $audio->storeAs($path_str,$name);

                    $file_name = $audio->getClientOriginalName();
                    $title = pathinfo($file_name, PATHINFO_FILENAME);;

                    $memories = new Memories();
                    $memories->title = $title;
                    $memories->filename = $path;
                    $memories->filetype = "record";
                    $memories->user_id = $user->id;
                    $memories->save();
                    $memoriesTmp[] = $memories;
                }

            return response()->json([
                'message' => 'Audio uploaded successfully.',
                'data' => $memoriesTmp
            ], 200);
        }

    }

    public function getAllAudioRecordById()
    {
        //Get the data
        $user = Auth::user();
        $user_type = $user->user_types->user_type;
        if(!empty($user_type) && $user_type==="beneficiary"){
            $user_id = $user->beneficiary_id;
        }else{
            $user_id = $user->id;
        }
       
        $imagesInfo = DB::table('memories')->where('user_id','=',$user_id)->where('filetype','=',"record")
        ->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function getRecentAudioRecordByDay($user_id,$day)
    {
        //Get the data
        $imagesInfo = DB::table('memories')->whereDate('created_at', Carbon::now()->subDays($day))->where('filetype','=',"record")->where('user_id','=',$user_id)->select('memories.*')->get();

        return response()->json($imagesInfo, 200);
    }

    public function deleteAudioRecordById($id)
    {
        //Get the task
        $memoriesInfo = Memories::findOrfail($id);
        //Delete file from disk.
        // $video_path = storage_path()."uploads/audios/".$memoriesInfo->user_id."/".$memoriesInfo->filename;
        $video_path = public_path('/').$memoriesInfo->filename;
        if (File::exists($video_path)) {
            Log::info("file path = ".$video_path);
            File::delete($video_path);
             
        }

        if($memoriesInfo->delete()) {
            return response()->json([
                'status'=>'success',
                'message' => 'Data deleted successfully!',
                'data' => $memoriesInfo
            ],200);
        }
    }
   
}