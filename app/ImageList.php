<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ImageList extends Model
{
    protected $fillable = ['user_id','image_type','image_url','status']; 
   
    public function user(){
        return $this->belongsTo(User::class);
    }
}
