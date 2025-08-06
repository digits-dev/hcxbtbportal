<?php

namespace App\Http\Controllers\OrderHistories;

use App\Exports\OrdersExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use App\Models\OrderHistoryLines;
use App\Models\OrderLines;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use DB;
use App\Models\AdmModels\AdmPrivileges;
use Maatwebsite\Excel\Facades\Excel;

class OrderHistoriesController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'orders.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

     public function getAllData(){
         if (CommonHelpers::myPrivilegeId() == AdmPrivileges::HOMECREDITSTAFF) {
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem'])->where('created_by', CommonHelpers::myId());
        }else {
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy']);

        }

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
        $data['order'] = Orders::leftJoin('statuses', 'statuses.id', 'orders.status')
            ->select('orders.*', 'statuses.name as status_name')
            ->where('orders.id', $id)
            ->first();
        $data['lines'] = OrderLines::leftJoin('item_masters', 'item_masters.digits_code', 'order_lines.digits_code')
        ->where('order_id', $id)->get();
        $data['my_privilege_id'] = CommonHelpers::myPrivilegeId();
 
        return Inertia::render("OrderHistories/ViewOrderHistoryDetails", $data);
    } 

       public function export(){
        $filename = "Order History - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();

        return Excel::download(new OrdersExport($query), $filename . '.xlsx');
    }
}