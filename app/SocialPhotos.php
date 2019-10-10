<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SocialPhotos extends Model
{
    //
    protected $fillable = ['id','title','filetype','social_type','image_url'];

    public function storePhotos($request)
    {
        try{
            $user_id = $request->user_id;
            $social_type = $request->social_type;
            $file_type = $request->file_type;
            $title = $request->title;
            $count = 0;
            foreach($request->imageList as $photo):
                if(!empty($photo))
                {
                    $newPhoto = SocialPhotos::firstOrNew(['image_url'=>$photo]);
                    if(empty($newPhoto->id))
                    {
                        $newPhoto->title        =   $request->tile;
                        $newPhoto->user_id      =   $request->user_id;
                        $newPhoto->social_type  =   $request->social_type;
                        $newPhoto->filetype     =   $request->file_type;
                        $newPhoto->image_url    =   $photo;
                        $newPhoto->save();
                        $count += 1;
                    } 
                }
            endforeach;
            $photoList = $this->where('user_id',$user_id)->get();
            $response = [
                'status'=>'success',
                'count'=>$count,
                'data'=>$photoList
            ];
        }catch(Exception $e)
        {
            $response = [
                'status'=>'error',
                'message'=>$e->getMessage()
            ];
        }
        return $response;
    }

    public function viewPhotos($user_id)
    {
        if(!empty($user_id))
        {
            $photoList = $this->where('user_id',$user_id)->get();
           
            if(count($photoList)>0)
            {
                $response = [
                    'status'=>'success',
                    'count'=>count($photoList),
                    'data'=>$photoList
                ];
            }else{
                $response = [
                    'status'=>'error',
                    'count'=>count($photoList),
                    'message'=>'Photos not found!'
                ];
            }

           
        }else
        {
            $response = [
                'status'=>'error',
                'message'=>'Photos not found!'
            ];
        }

        return $response;
    }
}
