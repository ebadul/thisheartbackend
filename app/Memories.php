<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memories extends Model
{
    //
    protected $fillable = [
        'title', 'filename', 'filetype'
    ];
    
}
