<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $fillable = [
        'user_id', 'friend_id', 'status',
    ];

    public function friend(){
        return $this->belongsTo('App\User','id','user_id');
    }

    public function info(){
        return $this->belongsTo('App\User','friend_id','id');
    }
}
