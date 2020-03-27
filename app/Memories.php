<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Memories extends Model
{
    //
    protected $fillable = [
        'title', 'filename', 'filetype'
    ];

    public function imageCount(){
        $user = Auth::user();
        $memoriesCount = $this::where('user_id','=',$user->id)->
        where('filetype','=','image')->count();
        return $memoriesCount;
    }
    public function videoCount(){
        $user = Auth::user();
        $memoriesCount = $this::where('user_id','=',$user->id)->
        where('filetype','=','video')->count();
        return $memoriesCount;
    }
    public function recordCount(){
        $user = Auth::user();
        $memoriesCount = $this::where('user_id','=',$user->id)->
        where('filetype','=','record')->count();
        return $memoriesCount;
    }

    
}
