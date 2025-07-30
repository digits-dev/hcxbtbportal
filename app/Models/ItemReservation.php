<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'status',
        'digits_code',
        'quantity',
        'created_at',
        'updated_at',
    ];
}