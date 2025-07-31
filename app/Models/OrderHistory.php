<?php

namespace App\Models;

use app\Helpers\CommonHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
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
        'approved_contract',
        'payment_proof',
        'rejected_payment_proof',
        'dp_receipt',
        'proof_of_delivery',
        'schedule_date',
        'transaction_type',
        'carrier_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'verified_by_acctg',
        'verified_at_acctg',
        'rejected_by',
        'rejected_at',
        'scheduled_by_logistics',
        'scheduled_at_logistics',
        'delivered_by_logistics',
        'delivered_at_logistics',
        'closed_by_ecomm',
        'closed_at_ecomm',
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
                    if (in_array($field, ['created_at', 'updated_at'])) {
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
                   if ($request->filled($field)) {
                    $data = $request->input($field);
                    $operator = $data['operator'] ?? '=';
                    $sorting = strtolower($data['sorting'] ?? '') === 'ascending' ? 'asc' : 'desc';
                    $value = $data['value'] ?? null;

                   switch ($operator) {
                        case 'Empty (or Null)':
                            $query->whereNull($field);
                            break;
    
                        case 'NOT IN':
                            $query->whereNotIn($field, is_array($value) ? $value : explode(',', $value));
                            break;
    
                        case 'IN':
                            $query->whereIn($field, is_array($value) ? $value : explode(',', $value));
                            break;
    
                        case '!= (Not Equal to)':
                            $query->where($field, '!=', $value);
                            break;
    
                        case '= (Equal to)':
                            $query->where($field, '=', $value);
                            break;
    
                        case 'NOT LIKE':
                            $query->where($field, 'NOT LIKE', "%$value%");
                            break;
    
                        case 'LIKE':
                        default:
                            $query->where($field, 'LIKE', "%$value%");
                            break;
                    }

                // Optional sorting logic
                if (!empty($data['sorting'])) {
                    $query->orderBy($field, $sorting);
                }

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
}
