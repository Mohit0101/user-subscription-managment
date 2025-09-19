<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code','discount_fixed','discount_percent','max_redemptions',
        'starts_at','ends_at','active'
    ];
    
    protected $casts = ['starts_at'=>'datetime','ends_at'=>'datetime','active'=>'boolean'];
}
