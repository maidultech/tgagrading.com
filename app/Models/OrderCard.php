<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCard extends Model
{

    function details(){
        return $this->belongsTo(OrderDetail::class,'order_details_id');
    }

    public function finalGrade()
    {
        return $this->belongsTo(FinalGrading::class, 'final_grading', 'finalgrade');
    }
}
