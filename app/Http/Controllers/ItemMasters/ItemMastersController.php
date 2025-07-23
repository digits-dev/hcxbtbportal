<?php

namespace App\Http\Controllers\ItemMasters;

use App\Exports\SubmasterExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ItemMastersController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'item_masters.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

    public function getAllData(){
        $query = ItemMaster::query();
        $filter = $query->searchAndFilter(request());
        $result = $filter->orderBy($this->sortBy, $this->sortDir);
        return $result;
    }


    public function getIndex()
    {
        if(!CommonHelpers::isView()) {
            return Inertia::render('Errors/RestrictionPage');
        }

        $data = [];
        $data['tableName'] = 'item_masters';
        $data['page_title'] = 'Item Master';
        $data['item_master'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("ItemMasters/ItemMasters", $data);
    }

    public function export()
    {

        $headers = [
            'Id',
            'Digits Code',
            'Item Description',
            'Model',
            'Color',
            'Size',
            'Created Date',
            'Updated Date',
        ];

        $columns = [
            'id',
            'digits_code',
            'item_description',
            'model',
            'actual_color',
            'size',
            'created_at',
            'updated_at',
        ];

        $filename = "Item Master - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();
        return Excel::download(new SubmasterExport($query, $headers, $columns), $filename . '.xlsx');

    }

    

}
