<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [

        'title',
        'description',
        'price',
        'user_id',
        'category_id',
    ];


    // relation de N->1  entre Ad et user, category
    public function user(){
        return $this ->belongsTo(User::class);
    }

    public function category(){
        return $this ->belongsTo(Category::class);
    }

    //relation de 1->N entre Ad et comment, order
    public function comment(){
        return $this->hasMany(Comment::class);
    }
    public function order(){
        return $this->hasMany(Order::class);
    }
}
