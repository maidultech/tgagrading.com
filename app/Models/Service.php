<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

        function serviceExtra(){
        return $this->hasOne(ServiceExtra::class,'service_id','id');
    }
}
