<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAdditionalCost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_additional_cost';

    protected $fillable = [
        'order_id',
        'details',
        'price',
    ];

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
