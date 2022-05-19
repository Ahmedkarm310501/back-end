<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        'name','price','Quantity','details','photo'
    ];
    public function photos(){

        return $this->hasMany(productPhoto::class, 'product_id');

    }
}
