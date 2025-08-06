<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statuses extends Model
{
    use HasFactory;

    Public const FOR_PAYMENT = 1;
    public const FOR_VERIFICATION = 2;
    public const ORDER_PROCESSING = 3;
    public const INCOMPLETE = 4;
    public const FOR_SCHEDULE = 5;
    public const FOR_DELIVERY = 6;
    public const TO_CLOSE = 7;
    public const CLOSED = 8;

    protected $fillable = [
        'id',
        'name',
        'color',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $filterable = [
        'name',
        'color',
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

    public function getCreatedBy() {
        return $this->belongsTo(AdmUser::class, 'created_by', 'id');
    }
    
    public function getUpdatedBy() {
        return $this->belongsTo(AdmUser::class, 'updated_by', 'id');
    }
}