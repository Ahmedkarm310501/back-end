<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'city',
        'type',
        'user_id',

    ];

    public function user(){

        return $this->belongsTo(User::class, 'user_id');

    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'address_id');
    }
}
