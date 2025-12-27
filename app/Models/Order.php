<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'ad_id',
        'buyer_id',
        'seller_id',
        'price',
        'status',
    ];
    //relation N->1 entre Order et Ad,User
    public function ad(){
        return $this->belongsTo(Ad::class);
    }

    public function buyer(){
        return $this->belongsTo(User::class,'buyer_id');
    }

    public function seller(){
        return $this->belongsTo(User::class,'seller_id');
    }

}
