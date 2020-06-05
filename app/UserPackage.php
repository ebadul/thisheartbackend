<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PackageInfo;
use App\PaymentSession;
use App\User;
use App\Beneficiary;
use App\Account;
use App\Letters;
use Stripe\Stripe;
use Auth;
use File;
use Crypt;


class UserPackage extends Model
{
    //
    protected $access_url = "";
    public function __construct(){
        Stripe::setApiKey('sk_test_9DkPWEVGZrgEo6q9EeZBDXlC00rgoKMYML');
        $this->access_url = Request()->headers->get('origin').'/';
    }

    protected $fillable = ['id','user_id','package_id','subscription_date','subscription_expire_date','subscription_status'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function package_info(){
        return $this->belongsTo(PackageInfo::class,'package_id','id');
    }

    public function package_entities(){
        return $this->hasMany(PackageEntity::class,'package_id','package_id')->with('entity_info');
    }

    
    public function checkPkgEntityActionStop($action_type){
        $user = Auth::user();
        $user_id = $user->id;
        switch($action_type){
            case "images":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Images')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "videos":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Videos')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "records":
                $user_package = $user->user_package;
                $user_package_entities = $user_package->package_entities;
                $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Audio Recording')->first();
                $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                $entity_value = $user_entity_details->entity_value;
                $memories = new Memories;
                $imageCount = $memories->imageCount();
                
                if($imageCount>=$entity_value){
                    return true;
                }else{
                    return false;
                }
                break;
            case "storages":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','File Storage')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        if($entity_value==="-1"){
                            return false;
                        }
                        $user_path = public_path('uploads/'.$user->id);
                        if (File::exists($user_path)) {
                            $user_storage_size = File::size($user_path);
                            $getAllDirs = File::directories($user_path);
                            $fileSize =[];
                            $totalFileSizeGB=0;
                            foreach( $getAllDirs as $dir ) {
                                $dirNames[] = basename($dir);
                                $fileList=File::files($user_path.'/'.basename($dir));
                                foreach($fileList as $fileTmp){
                                    $fileSize[] = ($fileTmp->getSize()/1024)/1027;
                                    $totalFileSizeGB+=(($fileTmp->getSize()/1024)/1024)/1024;
                                }
                            }

                            if($totalFileSizeGB>=$entity_value){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "beneficiaries":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Beneficiaries Saved')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $beneficiaries_count = Beneficiary::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($beneficiaries_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "accounts":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Accounts Saved')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $account_count = Account::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($account_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            case "letters":
                try{
                        $user_package = $user->user_package;
                        $user_package_entities = $user_package->package_entities;
                        $entity_info = PackageEntitiesInfo::where('package_entity_title','=','Encrypted Letters/Messages')->first();
                        $user_entity_details = $user_package_entities->where('package_entities_id','=',$entity_info->id)->first();
                        $entity_value = $user_entity_details->entity_value;
                        $letters_count = Letters::where('user_id',$user->id)->count();

                        if($entity_value==="-1"){
                            return false;
                        }

                        if($letters_count>=$entity_value){
                            return true; //generate error 
                        }else{
                            return false;
                        }

                    }catch(Exception $ex){
                        return new Exception($ex->getMessage());
                    }
                break;
            default:
                return response()->json([
                    'status'=>'success',
                    'data'=>'yes'
                ]);
        }
    }

    public function saveUserPackage($rs){
        $user_id = $rs['user_id'];
        $package_id =  $rs['package_id'];
        $date = date('Y-m-d');
        $package_info = PackageInfo::where('id','=',$package_id)->orderBy('id','desc')->first();
        $expire_date = date('Y-m-d', strtotime($date.' + '.$package_info->days.' days'));

        $user_pkg = UserPackage::where('user_id','=',$user_id)->first();
        if(empty($user_pkg)){
            $user_pkg = new UserPackage;
        }  
        $user_pkg->user_id = $user_id;
        $user_pkg->package_id = $package_id;
        $user_pkg->subscription_date = $date;
        $user_pkg->subscription_expire_date = $expire_date;
        $user_pkg->subscription_status = 1;
        if($user_pkg->save()){
            return $user_pkg;
        }else{
            return false;
        }

    }

    public function paymentCreateSession($rs){
        try{
                $user = Auth::user();
                \Stripe\Stripe::setApiKey('sk_test_9DkPWEVGZrgEo6q9EeZBDXlC00rgoKMYML');
                $success_url = $this->access_url.'payment-success/'.Crypt::encryptString('payment-success').'?session_id={CHECKOUT_SESSION_ID}';
                $cancel_url = $this->access_url."payment-cancel/".Crypt::encryptString('payment-cancel');
                $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => $rs->item,
                    'description' => $rs->description,
                    'images' => ['http://thisheart.co:8000/images/package-img.png'],
                    'amount' => $rs->amount*100,
                    'currency' => 'usd',
                    'quantity' => 1,
                ]],
                'metadata'=>[
                    'user_id' => $user->id,
                    'package_id' => $rs->item_id,
                    'amount' => $rs->amount*100
                ],
                'success_url' => $success_url,
                'cancel_url' =>  $cancel_url,
                ]);
            }catch(Exception $ex){
                $exp = $ex->getMessage();
            }
        if(!empty($session)){
            $session_rs = [
                'package_id'=>$rs->item_id,
                'payment_session_id' => $session->id,
                'paid' => 0,
                'amount' => $session->display_items[0]->amount,
                'user_package_id' => '0',
                'payment_details_id' => '0',
            ];
            $payment_session = new PaymentSession;
            $payment_session->savePaymentSession($session_rs);

            return [
                'status'=>'success',
                'data'=>$session
            ];
        }else{
            return [
                'status'=>'error',
                'data'=>$exp
            ];
        }
    }

    public function retriveSessionInfo($session_id){
        $session_status = \Stripe\Checkout\Session::retrieve(
            $session_id
          );
        return $session_status;
    }
    
    public function retrivePaymentInfo($payment_intent_id){
        $payment_intent = \Stripe\PaymentIntent::retrieve(
            $payment_intent_id
          );
        return $payment_intent;
    }

}
