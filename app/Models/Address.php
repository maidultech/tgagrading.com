<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'street',
        'apt_unit',
        'city',
        'zip_code',
        'country',
        'is_default',
        'state',
        'phone',
    ];
}
