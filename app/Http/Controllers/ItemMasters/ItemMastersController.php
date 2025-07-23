<?php

namespace App\Http\Controllers\ItemMasters;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

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
}
