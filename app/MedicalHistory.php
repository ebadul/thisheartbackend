<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    //
    protected $fillable = [
        'diagnosis_id', 'member_type', 'user_id'
    ];

}
