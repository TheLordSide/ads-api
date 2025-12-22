<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $fillable = [
    'name',
  ];

//liste de la relation 1->N entre Category et Ad
  public function ad(){

    return $this->hasMany(Ad::class);
  }
}
