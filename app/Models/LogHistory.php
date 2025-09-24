<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_history';

    protected $fillable = [
        'user_id',
        'action',
        'is_admin',
    ];


    public function user(){
        if($this->is_admin == 1){
            return $this->belongsTo(Admin::class,'user_id');
        }else{
            return $this->belongsTo(User::class,'user_id');
        }
    }
}
