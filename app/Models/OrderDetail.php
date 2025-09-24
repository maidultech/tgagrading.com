<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    /**
     * Get the productName
     *
     * @param  string  $value
     * @return string
     */
    public function getProductNameAttribute($value)
    {
        return "{$this->year} {$this->brand} {$this->cardNumber} {$this->playerName}";
    }

    public function cards(){
        return $this->hasMany(OrderCard::class, 'order_details_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

    public function gradedCards(): HasMany
    {
        return $this->hasMany(OrderCard::class, 'order_details_id')
                    ->where(function ($query) {
                        $query->where('final_grading', '>', 0)
                              ->orWhere('final_grading', '=', 'A');
                    });
    }
}
