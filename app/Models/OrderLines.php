<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLines extends Model
{
    use HasFactory;

    public function getHeader(){
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    public function getItem(){
        return $this->belongsTo(ItemMaster::class, 'digits_code', 'digits_code');
    }
}
