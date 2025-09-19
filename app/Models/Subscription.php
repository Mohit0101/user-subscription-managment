<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id','plan_id','starts_at','ends_at',
        'canceled_at','status','promo_code','promo_discount'
    ];
    protected $casts = [
        'starts_at'=>'datetime','ends_at'=>'datetime','canceled_at'=>'datetime'
    ];
    public function user(){ 
        return $this->belongsTo(User::class); 
    }
    public function plan(){ 
        return $this->belongsTo(Plan::class); 
    }
}
