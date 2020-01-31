<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiagnosisInfo extends Model
{
    //
    protected $fillable = [
        'id','diagnosis_name', 'description'
    ];

    public function getDiagnosisInfos(){
        $medical_history = $this->all();
        return $medical_history;
    }
}
