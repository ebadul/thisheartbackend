<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\SocialPhotos;

class SocialController extends Controller
{
    //

    public function storePhotos(Request $request)
    {
         
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->storePhotos($request);
        return response()->json( $photoList, 200);
        
    }

    public function viewPhotos($user_id)
    {
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->viewPhotos($user_id);
            
        if($photoList['status']=="success")
        {
            return response()->json(
                [
                    'data'=>$photoList
                ] 
             , 200);
        }else{
            return response()->json(
                [
                    'data'=>$photoList
                ] 
             , 500);
        }
        
        
    }

    public function updatePhotos(Request $request)
    {
         
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->storePhotos($request);
            
        return response()->json([
            'status' => 'success',
            'message' => 'Image uploaded successfully.',
            'data' => $photoList
        ], 200);
        
    }

    public function deletePhotos(Request $request)
    {
         
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->storePhotos($request);
            
        return response()->json([
            'status' => 'success',
            'message' => 'Image uploaded successfully.',
            'data' => $photoList
        ], 200);
        
    }
}
