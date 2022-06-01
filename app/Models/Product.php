<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[

        'name','price','Quantity','details','photo','category_id'


    ];


    public function category(){

        return $this->belongsTo(Categery::class, 'category_id');

    }
    public function carts(){

        return $this->hasMany(Cart::class, 'product_id');

    }
    public function favourite(){

        return $this->hasMany(Favourite::class, 'product_id');

    }
}




