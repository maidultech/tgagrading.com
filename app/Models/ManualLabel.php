<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualLabel extends Model
{
    
    protected $fillable = [
        'year',
        'brand_name',
        'card',
        'card_name',
        'item_name',
        'notes',
        'card_number',
        'grade',
        'grade_name',
        'qr_link',
        'surface',
        'centering',
        'corners',
        'edges',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    function createdBy(){
        return $this->belongsTo(Admin::class,'created_by');
    }

    function updatedBy(){
        return $this->belongsTo(Admin::class,'updated_by');
    }
}






