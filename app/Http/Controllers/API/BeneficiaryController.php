<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\User;
use App\Beneficiary;
use App\BeneficiaryUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BeneficiaryController extends BaseController
{
    public function addBeneficiary(Request $request)
    {
        //Log::info("Request = ".$request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'user_id' => 'required',
            'email' => 'required|email',
            'mail_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'last_4_beneficiary' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Validation error,please check input field.',
            ], 400);

        }else{

            if(is_null($request->mail_address2)){
                $request->mail_address2 = "";
            }

            $beneficiaryInfo = new Beneficiary();

            $beneficiaryInfo->first_name = $request->first_name;
            $beneficiaryInfo->last_name = $request->last_name;
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = $request->email;
            $beneficiaryInfo->mail_address = $request->mail_address;
            $beneficiaryInfo->mail_address2 = $request->mail_address2;
            $beneficiaryInfo->city = $request->city;
            $beneficiaryInfo->state = $request->state;
            $beneficiaryInfo->zip = $request->zip;
            $beneficiaryInfo->last_4_beneficiary = $request->last_4_beneficiary;
            $beneficiaryCode = str_random(16);
            $beneficiaryInfo->invite_code = $beneficiaryCode;
            $accUrlCode = str_random(8);
            $beneficiaryInfo->access_url = 'http://45.35.50.179:3000/beneficiary/access/'.$accUrlCode;

            $beneficiaryInfo->save();
            $user = User::where('id', '=', $request->user_id)->first();

            $beneficiaryLoginUrl = 'http://45.35.50.179:3000/beneficiary/login';
            //Log::info($request->user_id." user first_name ".$user->name." ben first_name ".$request->first_name);    
            //Send mail to beneficiary.
            $to_name = $request->first_name;
            $to_email = $request->email;
            $data = array(
                'b_first_name' => $request->first_name,
                'user_first_name' => $user->name,
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'last4_social' => $request->last_4_beneficiary,
                'login_url' => $beneficiaryLoginUrl
            );
 
            //Log::info("Before sending... ". $to_name ." to_email ".$to_email." user first_name ".$user->first_name);    
            
            Mail::send('emails.add-beneficiary', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Beneficiary Email');
                $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
            });

            return response()->json([
                'message' => 'Beneficiary added successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }

    public function resetBeneficiaryCode($id)
    {
        $beneficiaryInfo = Beneficiary::where('id', '=', $id)->first();
        $beneficiaryCode = str_random(16);
        
        if($beneficiaryInfo){
            $beneficiaryInfo->invite_code = $beneficiaryCode;
            $beneficiaryInfo->validate_code = 0;
            $beneficiaryInfo->save();

            $user = User::where('id', '=', $beneficiaryInfo->user_id)->first();

            //Log::info($request->user_id." user first_name ".$user->name." ben first_name ".$request->first_name);    
            //Send mail to beneficiary.
            $beneficiaryLoginUrl = 'http://45.35.50.179:3000/beneficiary/login';
            $to_name = $beneficiaryInfo->first_name;
            $to_email = $beneficiaryInfo->email;
            $data = array(
                'b_first_name' => $beneficiaryInfo->first_name,
                'user_first_name' => $user->name,
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'login_url' => $beneficiaryLoginUrl
            );
    
            $existingBeneficiaryUser = BeneficiaryUser::where('beneficiary_id', '=', $id)->first();
            if($existingBeneficiaryUser->delete()){
                Log::info("Existing Beneficiary User deleted.");
            }
            //Log::info("Before sending... ". $to_name ." to_email ".$to_email." user first_name ".$user->first_name);    
            
            Mail::send('emails.reset-beneficiary-code', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Reset Beneficiary Code Email');
                $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
            });

            return response()->json([
                'message' => 'Beneficiary code reset successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Beneficiary code reset failed!',
                'data' => $beneficiaryInfo
            ],400);
        }

    }

    public function sendNewBeneficiaryCode($id)
    {
        $beneficiaryInfo = Beneficiary::where('id', '=', $id)->first();
        $beneficiaryCode = str_random(16);
       
        if($beneficiaryInfo){
            $beneficiaryInfo->invite_code = $beneficiaryCode;
            $beneficiaryInfo->validate_code = 0;
            $beneficiaryInfo->save();

            $user = User::where('id', '=', $beneficiaryInfo->user_id)->first();

            //Log::info($request->user_id." user first_name ".$user->name." ben first_name ".$request->first_name);    
            //Send mail to beneficiary.
            $beneficiaryLoginUrl = 'http://45.35.50.179:3000/beneficiary/login';
            $to_name = $beneficiaryInfo->first_name;
            $to_email = $beneficiaryInfo->email;
            $data = array(
                'b_first_name' => $beneficiaryInfo->first_name,
                'user_first_name' => $user->name,
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'login_url' => $beneficiaryLoginUrl
            );

            $existingBeneficiaryUser = BeneficiaryUser::where('beneficiary_id', '=', $id)->first();
            if($existingBeneficiaryUser->delete()){
                Log::info("Existing Beneficiary User deleted.");
            }
    
            //Log::info("Before sending... ". $to_name ." to_email ".$to_email." user first_name ".$user->first_name);    
            
            Mail::send('emails.new-beneficiary-code', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('New Beneficiary Code Email');
                $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
            });

            return response()->json([
                'message' => 'New code send successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Beneficiary code send failed!',
                'data' => $beneficiaryInfo
            ],400);
        }

    }

    public function validateCode(Request $request)
    {
        $accessCode = $request->access_code;
        $urlCode = $request->url_code;
        $beneficiaryInfo = Beneficiary::where('invite_code', '=', $accessCode)->first();
        //$beneficiaryInfo = DB::table('beneficiaries')->where('invite_code','=',$accessCode)->select('beneficiaries.*')->get();

        if($beneficiaryInfo){
            if($beneficiaryInfo->validate_code == 0){

                if($beneficiaryInfo->invite_code == $accessCode){

                    $beneficiaryInfo->validate_code = 1;
                    $beneficiaryInfo->save();

                    return response()->json([
                        'message' => 'Code validated successfully!',
                        'validated' => 1,
                        'data' => $beneficiaryInfo
                    ],200);
                }else{
                    return response()->json([
                        'message' => 'Invalid code. Please try again.',
                        'validated' => 0
                    ],400);
                }
                
            }else{
                return response()->json([
                    'message' => 'This code already validated!',
                    'validated' => 1
                ],200);
            }
        }else{
            return response()->json([
                'message' => 'Invalid code. Please try again.',
                'validated' => 0
            ],400);
        }

    }

    public function getBeneficiaryById($user_id)
    {
        //Log::info("user_id = ".$user_id);
        //Get the data
        $beneficiaryInfo = DB::table('beneficiaries')->where('user_id','=',$user_id)->select('beneficiaries.*')->get();

        return response()->json($beneficiaryInfo, 200);
    }

    public function updateBeneficiaryById(Request $request,$id)
    {
        $beneficiaryInfo = Beneficiary::findOrfail($id);
        if($beneficiaryInfo){
            $beneficiaryInfo->first_name = $request->first_name;
            $beneficiaryInfo->last_name = $request->last_name;
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = $request->email;
            $beneficiaryInfo->mail_address = $request->mail_address;
            $beneficiaryInfo->mail_address2 = $request->mail_address2;
            $beneficiaryInfo->city = $request->city;
            $beneficiaryInfo->state = $request->state;
            $beneficiaryInfo->zip = $request->zip;
            $beneficiaryInfo->last_4_beneficiary = $request->last_4_beneficiary;

            $beneficiaryInfo->save();

            return response()->json([
                'message' => 'Data updated successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }else{
            return response()->json([
                'message' => 'Update failed! Data not found for this id.'
            ],404);
        }

    }

    public function deleteBeneficiaryById($id)
    {
        //Get the task
        $beneficiaryInfo = Beneficiary::findOrfail($id);
 
        if($beneficiaryInfo->delete()) {
            $beneficiaryUser = BeneficiaryUser::where('beneficiary_id', '=', $id)->first();
            if($beneficiaryUser->delete()){
                Log::info("beneficiaryUser deleted.");
            }

            return response()->json([
                'message' => 'Data deleted successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }

    public function validateLast4Social(Request $request)
    {
        $beneficiary_id = $request->beneficiary_id;
        $last4social_code = $request->last4social_code;

        $beneficiaryInfo = Beneficiary::where('id', '=', $beneficiary_id)->first();
        
        if($beneficiaryInfo){

            if($beneficiaryInfo->last_4_beneficiary == $last4social_code){

                return response()->json([
                    'message' => 'Code validated successfully!',
                    'validated' => 1,
                    'data' => $beneficiaryInfo
                ],200);
            }else{
                return response()->json([
                    'message' => 'Invalid code. Please try again.',
                    'validated' => 0
                ],400);
            }
           
        }else{
            return response()->json([
                'message' => 'Invalid beneficiary id. Please try again.'
            ],400);
        }

    }
}
