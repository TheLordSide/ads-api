<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'user_id',
        'ad_id',
    ];


    //relation 1->N entre comment et ad, user

    public function ad(){

        return $this->belongsTo(Ad::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
