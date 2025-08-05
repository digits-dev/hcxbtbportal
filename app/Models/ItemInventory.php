<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'digits_code',
        'qty',
        'created_at',
        'updated_at',
    ];

    protected $filterable = [
        'digits_code',
        'item_description',
        'qty',
        'created_at',
    ];

    
    public function scopeSearchAndFilter($query, $request){

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                foreach ($this->filterable as $field) {
                    if (in_array($field, ['created_at', 'updated_at'])) {
                        $query->orWhereDate($field, $search);
                    }
                    elseif ($field === 'digits_code') {
                        $query->orWhereHas('getItem', function ($query) use ($search) {
                            $query->where('digits_code', 'LIKE', "%$search%");
                        });
                    }
                    elseif ($field === 'item_description') {
                        $query->orWhereHas('getItem', function ($query) use ($search) {
                            $query->where('item_description', 'LIKE', "%$search%");
                        });
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

                    // FOR ELOQUENT
                    if ($field == 'digits_code' || $field == 'item_description'){
                        $query->orWhereHas('getItem', function ($q) use ($operator, $field, $value){
                            switch ($operator) {
                                case 'Empty (or Null)':
                                    $q->whereNull($field);
                                    break;

                                case 'NOT IN':
                                    $q->whereNotIn($field, is_array($value) ? $value : explode(',', $value));
                                    break;

                                case 'IN':
                                    $q->whereIn($field, is_array($value) ? $value : explode(',', $value));
                                    break;

                                case '!= (Not Equal to)':
                                    $q->where($field, '!=', $value);
                                    break;

                                case '= (Equal to)':
                                    $q->where($field, '=', $value);
                                    break;

                                case 'NOT LIKE':
                                    $q->where($field, 'NOT LIKE', "%$value%");
                                    break;

                                case 'LIKE':
                                default:
                                    $q->where($field, 'LIKE', "%$value%");
                                    break;
                            }
                        });
                    }
                    // FOR NORMAL
                    else{
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

    public function getItem() {
        return $this->belongsTo(ItemMaster::class, 'digits_code', 'digits_code');
    }

    public function getReserveItem() {
        return $this->hasMany(ItemReservation::class, 'digits_code', 'digits_code');
    }
}
