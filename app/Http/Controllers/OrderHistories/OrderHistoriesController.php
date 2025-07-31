<?php

namespace App\Http\Controllers\OrderHistories;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use App\Models\OrderHistoryLines;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class OrderHistoriesController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'order_histories.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

     public function getAllData(){
 
        $query = OrderHistory::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy']);

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
        $data['tableName'] = 'order_histories';
        $data['page_title'] = 'Order History';
        $data['order_histories'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("OrderHistories/OrderHistories", $data);
    }

    public function view ($id) {
        $data = [];
        $data['page_title'] = ' Order Details';
        $data['order'] = OrderHistory::leftJoin('statuses', 'statuses.id', 'order_histories.status')
            ->select('order_histories.*', 'statuses.name as status_name')
            ->where('order_histories.id', $id)
            ->first();
        $data['lines'] = OrderHistoryLines::leftJoin('item_masters', 'item_masters.digits_code', 'order_history_lines.digits_code')
        ->where('order_id', $id)->get();
        $data['my_privilege_id'] = CommonHelpers::myPrivilegeId();
 
        return Inertia::render("OrderHistories/ViewOrderHistoryDetails", $data);
    } 
}
