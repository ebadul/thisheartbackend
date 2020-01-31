<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\SocialPhotos;
use Auth;

class SocialController extends Controller
{
    //

    public function storePhotos(Request $request)
    {
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->storePhotos($request);
        return response()->json( $photoList, 200);
        
    }

    public function viewPhotos()
    {
        $socialPhotos = new SocialPhotos;
        $photoList = $socialPhotos->viewPhotos(Auth::user()->id);
         
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
             , 400);
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


    public function fetchInstagram(Request $request)
    {
         
        $socialPhotos = new SocialPhotos;
        //$photoList = $socialPhotos->storePhotos($request);
       

        $client_id=$request->client_id;
        $redirect_uri=$request->redirect_uri;
        $app_id=$request->app_id;
        $app_secret=$request->app_secret;
        $grant_type=$request->grant_type;
        $url='https://api.instagram.com/oauth/access_token';
        $code=$request->code;

        $request_fields = array(
            'client_id' => $client_id,
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'grant_type' => $grant_type,
            'redirect_uri' => $redirect_uri,
            'code' => $code
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $request_fields = http_build_query($request_fields);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_fields);
        $results = curl_exec($ch); 
        $results = json_decode($results,true);
        return response()->json([
            'status' => 'success',
            'message' => 'API hit successfully.',
            'result' => $results
        ], 200);
        
    }
}
