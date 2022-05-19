<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_path',
        'product_id',

    ];

    public function product(){

        return $this->belongsTo(Product::class, 'product_id');

    }

}
