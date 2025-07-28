<?php

namespace App\Models\AdmModels;

use app\Helpers\CommonHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmPrivileges extends Model
{
    use HasFactory;

    public const SUPERADMIN = 1;
    public const HOMECREDITSTAFF = 2;
    public const ACCOUNTING = 3;
    public const WAREHOUSE = 4;
    public const WIMS = 5;
    public const ECOMM = 6;
   

    protected $guarded = [];

    public function scopeGetData($query){
        return $query;
    }
    
    protected $fillable = [
        'id',
        'name',
        'is_superadmin',
        'theme_color',
        'created_at',
        'updated_at'
    ];

    protected $filterable = [
        'name',
        'is_superadmin',
        'theme_color',
        'created_at',
        'updated_at'
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
}
