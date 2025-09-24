<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'shipping_method_service_code' => 'array',
        'shipping_api_response' => 'array',
    ];
    public function transaction(){
        return $this->hasOne(Transaction::class, 'order_id' );
    }

    function details(){
        return $this->hasMany(OrderDetail::class,'order_id');
    }


    function additionalCosts(){
        return $this->hasMany(OrderAdditionalCost::class,'order_id');
    }

    public function rUser(){
        return $this->belongsTo(User::class, 'user_id' );
    }

    public function rPlan(){
        return $this->belongsTo(Plan::class, 'plan_id' );
    }

    public function cards(){
        return $this->hasMany(OrderCard::class, 'order_id');
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function getPlanPrice()
    {
        $plan = $this->rPlan;

        if (!$plan) {
            return null;
        }

        if ($plan->type === 'subscription') {
            // Get the price from the 'single' type plan
            $singlePlan = Plan::where('type', 'single')->first();
            return $singlePlan ? $singlePlan->price : null;
        }

        return $plan->price;
    }

    // For all graded card including No graded checked card
    public function isGradedCards() {
        return $this->hasMany(OrderCard::class, 'order_id') 
                    ->where(function ($query) {
                        $query->where('is_graded', 1);
                    });
    }

    // For all actual graded card
    public function gradedCards() {
        return $this->hasMany(OrderCard::class, 'order_id') 
                    ->where(function ($query) {
                        $query->where('final_grading', '>', 0)
                              ->orWhere('final_grading', '=', 'A');
                    });
    }
}
