<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'products',
        'total_price',
        'status',
        'user_id',
        'address_id',
        'delivery_type'


    ];
    /**
     * Get the user associated with the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salesReturn(): HasOne
    {
        return $this->hasOne(salesReturn::class, 'order_id');
    }

    public function user(){

        return $this->belongsTo(User::class, 'user_id');

    }
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'address_id');
    }
}
