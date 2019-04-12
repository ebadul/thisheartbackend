<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiagnosisInfo extends Model
{
    //
    protected $fillable = [
        'diagnosis_name', 'description'
    ];
}
