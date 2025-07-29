<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLines extends Model
{
    use HasFactory;


    public function getItem(){
        return $this->belongsTo(ItemMaster::class, 'item_code', 'digits_code');
    }
}
