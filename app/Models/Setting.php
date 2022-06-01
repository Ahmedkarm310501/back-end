<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'app_name',
        'facebook',
        'linkedin',
        'twitter',
        'instagram',
        'about_us',
        'terms',
        'money_back_policy',
        'privacy_statement',


    ];
}
