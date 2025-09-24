<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBrand extends Model
{
    use HasFactory;

    protected $table    = 'item_brands';
    protected $fillabel = [
        'name', 
        'status', 
        'order_id',
    ];
}
