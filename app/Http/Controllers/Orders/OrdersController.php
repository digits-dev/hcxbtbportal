<?php

namespace App\Http\Controllers\Orders;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
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
        $query = Orders::query()->with(['getCreatedBy', 'getUpdatedBy']);
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
        $data['tableName'] = 'orders';
        $data['page_title'] = 'Orders';
        $data['orders'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("Orders/Orders", $data);
    }

    public function create() {

        $data = [];
        $data['page_title'] = 'Create Order';
        $data['sku'] = DB::connection('mysql2')->table('item_masters')
        ->whereIn('warehouse_categories_id', [22])
        ->select('id','upc_code', 'item_description', 'model','actual_color', 'size' )->get();
 
        return Inertia::render("Orders/CreateOrderForm", $data);
    }


    public function store (Request $request) {
     $timestamp = now()->timestamp;
     $data = [
            'reference_number' => Orders::generateReferenceNumber(),
            'customer_name' => $request->customer_name,
            'delivery_address' => $request->delivery_address,
            'email_address' => $request->email_address,
            'contact_details' => $request->contact_details,
            'contact_details' => $request->contact_details,
            'financed_amount' => $request->financed_amount,
            'has_downpayment' => $request->has_downpayment,
            'downpayment_value' => $request->downpayment_value,
            'item_id' => $request->item_id,
            'approved_contract' => $timestamp . '_' . $request->approved_contract->getClientOriginalName(),
        ];
        if ($request->hasFile('approved_contract')) {
            $file = $request->file('approved_contract');  
            $filename = $timestamp . '_' . $request->approved_contract->getClientOriginalName();  
            $file->move(public_path('contract/uploaded-contract'), $filename);  
        }
        
            Orders::create($data);
        
        return redirect ('/orders');
    }

}