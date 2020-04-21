<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    protected $fillable = [
        'diagnosis_id', 'member_type', 'user_id'
    ];

    public function getMedicalHistory(){
        $medical_history = $this->all();
        return $medical_history;
    }

    public function diagnosisInfo(){
        
        return $this->belongsTo(DiagnosisInfo::class,'diagnosis_id');
    }

}
