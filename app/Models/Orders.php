<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\CommonHelpers;

class Orders extends Model
{
    use HasFactory;

      protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = CommonHelpers::myId();
        });

        static::updating(function ($model) {
            $model->updated_by = CommonHelpers::myId();
        });
    }


    protected $fillable = [
        'id',
        'reference_number',
        'status',
        'first_name',
        'last_name',
        'delivery_address',
        'email_address',
        'contact_details',
        'has_downpayment',
        'downpayment_value',
        'financed_amount',
        'item_id',
        'approved_contract',
        'payment_proof',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $filterable = [
        'reference_number',
        'status',
        'first_name',
        'last_name',
        'delivery_address',
        'email_address',
        'contact_details',
        'has_downpayment',
        'downpayment_value',
        'financed_amount',
        'item_id',
        'approved_contract',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

     public function scopeSearchAndFilter($query, $request){

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                foreach ($this->filterable as $field) {
                    if ($field === 'created_by') {
                        $query->orWhereHas('getCreatedBy', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%");
                        });
                    }
                    else if ($field === 'status') {
                        $query->orWhere($field, '=', $search);
                    }
                    elseif ($field === 'updated_by')  {
                        $query->orWhereHas('getUpdatedBy', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%");
                        });
                    } 
                    elseif (in_array($field, ['created_at', 'updated_at'])) {
                        $query->orWhereDate($field, $search);
                    }
                    else {
                        $query->orWhere($field, 'LIKE', "%$search%");
                    }
                }
            });
        }

        foreach ($this->filterable as $field) {
            if ($request->filled($field)) {
                $value = $request->input($field);
                if ($field === 'status') {
                    $query->where($field, '=', $value);
                }
                else{
                    $query->where($field, 'LIKE', "%$value%");
                }
            }
        }
    
        return $query;
        
    }

    public function getStatus() {
        return $this->belongsTo(Statuses::class, 'status', 'id');
    }

    public function getCreatedBy() {
        return $this->belongsTo(AdmUser::class, 'created_by', 'id');
    }
    
    public function getUpdatedBy() {
        return $this->belongsTo(AdmUser::class, 'updated_by', 'id');
    }

    public static function generateReferenceNumber()
    {
        $maxId = self::max('id'); 
        $nextId = $maxId + 1; 
        return 'HC' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
    
}