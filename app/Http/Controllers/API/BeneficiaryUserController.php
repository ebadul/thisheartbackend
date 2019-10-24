<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\BeneficiaryUser;

class BeneficiaryUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function beneficiary_user (){
        $beneficiary_accounts =  BeneficiaryUser:: all ()->toArray();
     
        return view ('admin.beneficiary_user', ['beneficiary_accounts'=>$beneficiary_accounts]);
    }

}