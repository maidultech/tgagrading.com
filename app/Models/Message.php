<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_messages';

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['ticket'];


    public function ticket() {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }


    public function sender(){
        if($this->msg_from == 1){
            return $this->belongsTo(User::class,'created_by');
        }else{
            return $this->belongsTo(Admin::class,'created_by');
        }
    }
}
