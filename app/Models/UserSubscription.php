<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $table='user_subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_card_peryear',
        'order_card_peryear',
        'year_start',
        'year_end',
    ];
}

