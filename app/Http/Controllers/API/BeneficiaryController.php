<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Illuminate\Http\Request;
use App\User;
use App\Beneficiary;
use App\BeneficiaryUser;
use App\WizardStep;
use App\UserPackage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Auth;

class BeneficiaryController extends BaseController
{
    // protected $access_url = "http://45.35.50.179/";
     protected $access_url = "";
    public function __construct()
    {
        $this->access_url = Request()->headers->get('origin').'/';
    }

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
            $user_package = new UserPackage;
            $package_storage_action = $user_package->checkPkgEntityActionStop("beneficiaries");
            if($package_storage_action){
                return response()->json([
                    'status'=>'error',
                    'code'=>'exceeds-beneficiaries',
                    'message' => "Sorry, your package exceeds the beneficiaries saved limit",
                    'storage'=>$package_storage_action
                ], 500); 
            }


            if(is_null($request->mail_address2)){
                $request->mail_address2 = "";
            }

            $beneficiaryInfo = new Beneficiary();

            $beneficiaryInfo->first_name = Crypt::encryptString($request->first_name);
            $beneficiaryInfo->last_name = Crypt::encryptString($request->last_name);
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = Crypt::encryptString($request->email);
            $beneficiaryInfo->mail_address = Crypt::encryptString($request->mail_address);
            $beneficiaryInfo->mail_address2 = Crypt::encryptString($request->mail_address2);
            $beneficiaryInfo->city = Crypt::encryptString($request->city);
            $beneficiaryInfo->state = Crypt::encryptString($request->state);
            $beneficiaryInfo->zip = Crypt::encryptString($request->zip);
            $beneficiaryInfo->last_4_beneficiary = Crypt::encryptString($request->last_4_beneficiary);
            $beneficiaryCode = str_random(16);
            $beneficiaryInfo->invite_code = $beneficiaryCode;
            $accUrlCode = str_random(8);
            $beneficiaryInfo->access_url = $this->access_url.'beneficiary/access/'.$accUrlCode;

            $beneficiaryInfo->save();
            $user = User::where('id', '=', $request->user_id)->first();

            $beneficiaryLoginUrl = $this->access_url.'login';
            //Log::info($request->user_id." user first_name ".$user->name." ben first_name ".$request->first_name);    
            //Send mail to beneficiary.
            $to_name = $request->first_name;
            $to_email = $request->email;
            $data = array(
                'b_first_name' => $request->first_name,
                'user_first_name' => Crypt::decryptString($user->name),
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'last4_social' => $request->last_4_beneficiary,
                'login_url' => $beneficiaryLoginUrl
            );
 
            $rsStep = (Object) [
                'step' => 'step-05',
                'info' => 'onboardbenefi_select'
            ];

            $wizStep = new WizardStep;
            $wizStep->setSteps($rsStep);
            
            //Log::info("Before sending... ". $to_name ." to_email ".$to_email." user first_name ".$user->first_name);    
            
            Mail::send('emails.add-beneficiary', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Beneficiary Email');
                $message->from('thisheartmailer@gmail.com','ThisHeart Mail Server');
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
            $beneficiaryLoginUrl = $this->access_url.'login';
            $to_name = Crypt::decryptString($beneficiaryInfo->first_name);
            $to_email = Crypt::decryptString($beneficiaryInfo->email);
            $data = array(
                'b_first_name' => Crypt::decryptString($beneficiaryInfo->first_name),
                'user_first_name' => Crypt::decryptString($user->name),
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'login_url' => $beneficiaryLoginUrl
            );
    
            $existingBeneficiaryUser = BeneficiaryUser::where('beneficiary_id', $id)->first();
            if(!empty($existingBeneficiaryUser)){
                $existingBeneficiaryUser->delete();
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
            $beneficiaryLoginUrl = $this->access_url.'login';
            $to_name = Crypt::decryptString($beneficiaryInfo->first_name);
            $to_email = Crypt::decryptString($beneficiaryInfo->email);
            $data = array(
                'b_first_name' => Crypt::decryptString($beneficiaryInfo->first_name),
                'user_first_name' => Crypt::decryptString($user->name),
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
        $urlWithCode = $request->url_code;
        $beneficiaryInfo = Beneficiary::where('invite_code', '=', $accessCode)->first();
        //$beneficiaryInfo = DB::table('beneficiaries')->where('invite_code','=',$accessCode)->select('beneficiaries.*')->get();
      
        if($beneficiaryInfo){
            if($beneficiaryInfo->validate_code == 0){

                if($beneficiaryInfo->invite_code == $accessCode &&
                  $beneficiaryInfo->access_url == $urlWithCode){

                    $beneficiaryInfo->validate_code = 1;
                    $beneficiaryInfo->save();
                    $beneficiaryInfo->email = Crypt::decryptString($beneficiaryInfo->email);
                    $beneficiaryInfo->first_name = Crypt::decryptString($beneficiaryInfo->first_name);
                    $beneficiaryInfo->last_name = Crypt::decryptString($beneficiaryInfo->last_name);
                    $beneficiaryInfo->last_4_beneficiary = Crypt::decryptString($beneficiaryInfo->last_4_beneficiary);
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
                $beneficiaryInfo->email = Crypt::decryptString($beneficiaryInfo->email);
                $beneficiaryInfo->first_name = Crypt::decryptString($beneficiaryInfo->first_name);
                $beneficiaryInfo->last_name = Crypt::decryptString($beneficiaryInfo->last_name);
                $beneficiaryInfo->last_4_beneficiary = Crypt::decryptString($beneficiaryInfo->last_4_beneficiary);
               
                return response()->json([
                    'message' => 'This code already validated!',
                    'validated' => 1,
                    'data' => $beneficiaryInfo
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

        foreach($beneficiaryInfo as $value){
            $value->first_name = Crypt::decryptString($value->first_name);
            $value->last_name = Crypt::decryptString($value->last_name);
            $value->email = Crypt::decryptString($value->email);
            $value->mail_address = !empty($value->mail_address)?Crypt::decryptString($value->mail_address):'';
            $value->mail_address2 = !empty($value->mail_address2)?Crypt::decryptString($value->mail_address2):''; 
            $value->city = !empty($value->city)?Crypt::decryptString($value->city):'';
            $value->state = !empty($value->state)?Crypt::decryptString($value->state):'';
            $value->zip = !empty($value->zip)?Crypt::decryptString($value->zip):'';
            $value->last_4_beneficiary = !empty($value->last_4_beneficiary)?Crypt::decryptString($value->last_4_beneficiary):'';
        }

        return response()->json($beneficiaryInfo, 200);
    }

    public function updateBeneficiaryById(Request $request,$id)
    {
        $beneficiaryInfo = Beneficiary::findOrfail($id);
        if($beneficiaryInfo){
            $beneficiaryInfo->first_name = Crypt::encryptString($request->first_name);
            $beneficiaryInfo->last_name = Crypt::encryptString($request->last_name);
            $beneficiaryInfo->user_id = $request->user_id;
            $beneficiaryInfo->email = Crypt::encryptString($request->email);
            $beneficiaryInfo->mail_address = Crypt::encryptString($request->mail_address);
            $beneficiaryInfo->mail_address2 = Crypt::encryptString($request->mail_address2);
            $beneficiaryInfo->city = Crypt::encryptString($request->city);
            $beneficiaryInfo->state = Crypt::encryptString($request->state);
            $beneficiaryInfo->zip = Crypt::encryptString($request->zip);
            $beneficiaryInfo->last_4_beneficiary = Crypt::encryptString($request->last_4_beneficiary);

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
            try{
                    $beneficiaryUser = BeneficiaryUser::where('beneficiary_id', '=', $id)->first();
                    $beneficiaryUserEmail = !empty($beneficiaryUser)?$beneficiaryUser->email:'';
                    $beneficiaryUserInfo = User::where('email','=',$beneficiaryUserEmail)->first();
                    if(!empty($beneficiaryUser) && $beneficiaryUser->delete()){
                        Log::info("beneficiaryUser deleted.");
                        if(!empty($beneficiaryUserInfo)){
                            $beneficiaryUserInfo->delete();
                        }
                        
                    }
            }catch(Exception $ex){
                return response()->json([
                    'message' => $ex->getMessage(),
                    'status' => 'error'
                ],400);
            }
            return response()->json([
                'status'=>'success',
                'message' => 'Data deleted successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }

    public function validateLast4Social(Request $request)
    {
        $beneficiary_id = $request->beneficiary_id;
        $last4social_code = Crypt::decryptString($request->last4social_code);

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

    public function saveBeneficiaryOnBoard(Request $rs){
        $validator = Validator::make($rs->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'last_4_social' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status'=>'error',
                'message' => 'Validation error,please check input field.',
            ], 400);

        }else{
            $user = Auth::user();
            $beneficiary_count = Beneficiary::where('user_id','=',$user->id)->count();
            if($beneficiary_count>1){
                return response()->json([
                    'status'=>'error',
                    'message' => "Sorry, To more than 2 beneficiary users isn't allowed for current package.",
                ], 500);
    
            }
            $beneficiaryInfo = new Beneficiary();
            $beneficiaryInfo->first_name = Crypt::encryptString($rs->first_name);
            $beneficiaryInfo->last_name = Crypt::encryptString($rs->last_name);
            $beneficiaryInfo->user_id = $user->id;
            $beneficiaryInfo->email = Crypt::encryptString($rs->email);
            $beneficiaryInfo->mail_address = Crypt::encryptString($rs->mail_address);
            $beneficiaryInfo->mail_address2 = '';
            $beneficiaryInfo->city = '';
            $beneficiaryInfo->state = '';
            $beneficiaryInfo->zip = '';
            $beneficiaryInfo->last_4_beneficiary = Crypt::encryptString($rs->last_4_social);
            $beneficiaryCode = str_random(16);
            $beneficiaryInfo->invite_code = $beneficiaryCode;
            $accUrlCode = str_random(8);
            $beneficiaryInfo->access_url = $this->access_url.'beneficiary/access/'.$accUrlCode;
            $beneficiaryInfo->save();

            $beneficiaryLoginUrl = $this->access_url.'login';
            
            $rsStep = (Object) [
                'step' => 'step-05',
                'info' => 'onboardbenefi_select'
            ];

            $wizStep = new WizardStep;
            $wizStep->setSteps($rsStep);
            //Send mail to beneficiary.
            $to_name = $rs->first_name;
            $to_email = $rs->email;
            $data = array(
                'b_first_name' => $rs->first_name,
                'user_first_name' => Crypt::decryptString($user->name),
                'url' => $beneficiaryInfo->access_url,
                'beneficiary_code' => $beneficiaryCode,
                'last4_social' => $rs->last_4_social,
                'login_url' => $beneficiaryLoginUrl
            );
          
            Mail::send('emails.add-beneficiary', $data, function($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Beneficiary Email');
                $message->from('thisheartmailer@gmail.com','This-Heart Mail Server');
            });

            return response()->json([
                'status'=>'success',
                'message' => 'Beneficiary added successfully!',
                'data' => $beneficiaryInfo
            ],200);
        }
    }
}
