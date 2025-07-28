<?php

namespace App\Http\Controllers\ItemInventories;

use App\Exports\SubmasterExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\ItemInventory;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ItemInventoriesController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'item_inventories.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

    public function getAllData(){
        $query = ItemInventory::query()->with(['getItem']);
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
        $data['tableName'] = 'item_inventories';
        $data['page_title'] = 'Item Inventory';
        $data['item_inventories'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("ItemInventories/ItemInventories", $data);
    }

    public function export()
    {

        $headers = [
            'Digits Code',
            'Item Description',
            'Stock',
            'Created Date',
        ];

        $columns = [
            'getItem.digits_code',
            'getItem.item_description',
            'qty',
            'created_at',
        ];

        $filename = "Item Inventory - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();
        return Excel::download(new SubmasterExport($query, $headers, $columns), $filename . '.xlsx');

    }

        public function checkInventory($digits_code)
        {
            $qty = ItemInventory::where('digits_code', $digits_code)->value('qty');

            return response()->json([
                'qty' => $qty ?? 0,
            ]);
        }

}