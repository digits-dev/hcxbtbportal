<?php

namespace App\Http\Controllers\Orders;

use App\Exports\OrdersExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\OrderLines;
use App\Models\ItemMaster;
use App\Models\ItemReservation;
use App\Models\ItemInventory;
use App\Models\Statuses;
use App\Models\AdmModels\AdmPrivileges;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\OrderConfirmationMail;
use App\Mail\SendProofOfPaymentLink;
use App\Mail\ReSendProofOfPaymentLink;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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
        if (CommonHelpers::myPrivilegeId() == AdmPrivileges::HOMECREDITSTAFF) {
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem'])->where('created_by', CommonHelpers::myId());
        }else if (CommonHelpers::myPrivilegeId() == AdmPrivileges::ACCOUNTING) {
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem'])->whereIn('status',[Statuses::FOR_VERIFICATION]);
        }else if (CommonHelpers::myPrivilegeId() == AdmPrivileges::LOGISTICS){
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem'])->whereIn('status',[Statuses::FOR_SCHEDULE, Statuses::FOR_DELIVERY]);
        }else if (CommonHelpers::myPrivilegeId() == AdmPrivileges::ECOMM){
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem'])->whereIn('status',[Statuses::TO_CLOSE]);
        }else {
            $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy', 'getLines.getItem']);
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
        $data['tableName'] = 'orders';
        $data['page_title'] = 'Orders';
        $data['orders'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("Orders/Orders", $data);
    }

    public function create() {
        $data = [];
        $data['page_title'] = 'Create Order';
        $data['sku'] = DB::table('item_masters')
            ->leftJoin('item_inventories', 'item_inventories.digits_code', '=', 'item_masters.digits_code')
            ->where('item_inventories.qty', '>', 1)
            ->select('item_masters.digits_code', 'item_description', 'model', 'actual_color', 'size')
            ->get();

 
        return Inertia::render("Orders/CreateOrderForm", $data);
    }


    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'delivery_address'   => 'required|string|max:255',
            'email_address'      => 'required|email|max:255',
            'contact_details' => [
                'required',
                'string',
                'regex:/^\+639\d{9}$/'
            ],
            'downpayment_value' => [
                'required_if:has_downpayment,yes',
                Rule::when($request->has_downpayment === 'yes', [
                    'decimal:0,2',
                ])
            ],
            'financed_amount'    => 'required|decimal:0,2',
            'has_downpayment'    => 'required|in:yes,no',
            'approved_contract'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',

        ],[
            'contact_details.required' => 'The contact number is required.',
            'contact_details.regex' => 'The contact number must be a valid Philippine mobile number starting with +63.',
        ]);

        // Check DEM DB connection first
        try {
            DB::connection('dem')->getPdo(); // Try connecting
        } catch (\Exception $e) {
            return back()->with(['message' => 'Unable to connect to the DEM database. Please try again later.', 'type' => 'error']);
        }
                
        try {

            DB::beginTransaction();
            // Handle file upload
            $timestamp = now()->timestamp;

            if ($request->hasFile('approved_contract')) {
                $file = $request->file('approved_contract');
                $filename = $timestamp . '_' . $file->getClientOriginalName();
                $file->move(public_path('contract/uploaded-contract'), $filename);
            }

            if ($validatedData['has_downpayment'] === 'yes') {
                $status = Statuses::FOR_PAYMENT;
            } else {
                $status =  Statuses::ORDER_PROCESSING;
            };

            // Prepare order data
            $orderData = [
                'reference_number'   => Orders::generateReferenceNumber(),
                'status'             => $status,
                'first_name'      => $validatedData['first_name'],
                'last_name'      => $validatedData['last_name'],
                'delivery_address'   => $validatedData['delivery_address'],
                'email_address'      => $validatedData['email_address'],
                'contact_details'    => $validatedData['contact_details'],
                'financed_amount'    => $validatedData['financed_amount'],
                'has_downpayment'    => $validatedData['has_downpayment'],
                'downpayment_value'  => $validatedData['downpayment_value'] ?? null,
                'approved_contract'  => $filename ?? null,
                'order_date'         => now(),
            ];

            // Create order
            $order = Orders::create($orderData);
            $orderId = $order->id;

            if (empty($request->items)) {
                DB::rollback();
                return back()->with(['message' => 'Please add an Item', 'type' => 'error']);
            }

            // Prepare order lines
            $lines = [];
            foreach ($request->items as $item) {
                $lines[] = [
                    'order_id'    => $orderId,
                    'digits_code' => $item['digits_code'],
                    'qty'         => $item['quantity'],
                    'created_at'  => now(),
                ];
            self::reserveItem($orderId, $item['digits_code'], $item['quantity']);
            
            }


            OrderLines::insert($lines);

            // Prepare and send email
            $encryptedId = Crypt::encryptString($orderId);

            // add items for sending order confirmation
            $orderData['items'] = OrderLines::join('item_masters', 'item_masters.digits_code', '=', 'order_lines.digits_code')
                ->where('order_lines.order_id', $orderId)
                ->select('item_masters.item_description', 'order_lines.qty')
                ->get()
                ->toArray();
 
            if ($validatedData['has_downpayment'] === 'yes') {
                Mail::to($validatedData['email_address'])->send(new SendProofOfPaymentLink([
                    'reference_number' => $order->reference_number,
                    'customer_name' => $validatedData['first_name'] . " " .$validatedData['last_name'],
                    'payment_link'  => url('/upload/' . $encryptedId),
                ]));
            } else {
                Mail::to($validatedData['email_address'])->send(new OrderConfirmationMail($orderData));
                self::createDemTransaction($orderId);
            }

            DB::commit();
            return redirect('/orders')->with(['message' => 'Order creation Success', 'type' => 'success']);
        }
        catch (\Exception $e) {
            DB::rollback();
            CommonHelpers::LogSystemError('Orders', $e->getMessage());
            return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
        }

    }

    public function reserveItem($orderId, $digits_code, $quantity)
    {
        $item = ItemInventory::where('digits_code', $digits_code)->firstOrFail();
        $availableQty = $item->qty - $item->reserved_qty;

        // if ($quantity > $availableQty) {
        //     throw new \Exception("Not enough stock for item: $digits_code");
        // }

        // Reserve the quantity
        $item->reserved_qty += $quantity;
        $item->save();

        // Record the reservation
        ItemReservation::create([
            'order_id'     => $orderId,
            'digits_code'  => $digits_code,
            'quantity'     => $quantity,
            'status'       => 'reserved',
        ]);
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
 
        return Inertia::render("Orders/ViewOrderDetails", $data);
    } 

    public function update($id) {
        
        $data = [];
        $data['order'] = Orders::leftJoin('statuses', 'statuses.id', 'orders.status')
            ->select('orders.*', 'statuses.name as status_name')
            ->where('orders.id', $id)
            ->first();
        $data['lines'] = OrderLines::leftJoin('item_masters', 'item_masters.digits_code', 'order_lines.digits_code')
        ->where('order_id', $id)->get();
 
        if(CommonHelpers::myPrivilegeId() == AdmPrivileges::ACCOUNTING) {
            return Inertia::render("Orders/AccountingVerification", $data);
        }else if (CommonHelpers::myPrivilegeId() == AdmPrivileges::LOGISTICS){
                if ($data['order']->status == Statuses::FOR_SCHEDULE) {
                    return Inertia::render("Orders/LogisticsSchedule", $data);
                }else {
                    return Inertia::render("Orders/LogisticsDelivery", $data);
                }
        }else if (CommonHelpers::myPrivilegeId() == AdmPrivileges::ECOMM) {
            return Inertia::render("Orders/EcommClose", $data);
        }
    }
        

    public function updateSave(Request $request) {

        DB::beginTransaction();
    
        $order = Orders::where('id', $request->order_id)->first();

        if (!$order){
                return back()->with(['message' => 'Order not Found', 'type' => 'error']);
        }
    
        if ($order->status == Statuses::FOR_VERIFICATION) {

            $existingRejectedProofs = $order->rejected_payment_proof
                ? explode(',', $order->rejected_payment_proof)
                : [];

            if ($order->payment_proof) {
                $existingRejectedProofs[] = $order->payment_proof;
            }
                
            if ($request->action == 'incomplete') {

                try{
                    Orders::where('id', $request->order_id)->update([
                        'status' => Statuses::INCOMPLETE,
                        'rejected_payment_proof' => implode(',', $existingRejectedProofs),
                        'payment_proof' => null,
                        'incomplete_reason' => $request->reason,
                        'rejected_by' => CommonHelpers::myId(),
                        'rejected_at' => now(),
                    ]);
    
                    $encryptedId = Crypt::encryptString($order->id);
    
                    Mail::to($order->email_address)->send(new ReSendProofOfPaymentLink([
                        'reference_number' => $order->reference_number,
                        'incomplete_reason' => $request->reason,
                        'customer_name' => $order->first_name." ".$order->last_name,
                        'payment_link' => url('/upload/' . $encryptedId),
                    ]));
    
                    DB::commit();
                    return redirect('/orders')->with(['message' => 'Order has been Rejected', 'type' => 'success']);
                }
                catch (\Exception $e) {
                    DB::rollback();
                    CommonHelpers::LogSystemError('Orders', $e->getMessage());
                    return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
                }  

            }else {

                $request->validate([
                    'dp_receipt'  => 'required|file|mimes:jpg,jpeg,png|max:2048',
                ]);

                try{
                    $timestamp = now()->timestamp;
                        if ($request->hasFile('dp_receipt')) {
                            $file = $request->file('dp_receipt');
                            $filename = $timestamp . '_' . $file->getClientOriginalName();
                            $file->move(public_path('dp-receipt/uploaded-receipt'), $filename);
                        }
                
                    Orders::where('id', $request->order_id)->update([
                        'status' => Statuses::ORDER_PROCESSING,
                        'dp_receipt' => $filename,
                        'verified_by_acctg' => CommonHelpers::myId(),
                        'verified_at_acctg' => now(),
                    ]);
    
                    $orderData = $order->toArray();
                    $orderData['items'] = OrderLines::join('item_masters', 'item_masters.digits_code', '=', 'order_lines.digits_code')
                                            ->where('order_lines.order_id', $order->id)
                                            ->select('item_masters.item_description', 'order_lines.qty')
                                            ->get()
                                            ->toArray();
    
                    Mail::to($order->email_address)->send(new OrderConfirmationMail($orderData));
    
                    self::createDemTransaction($order->id);
    
                    DB::commit();
                    return redirect('/orders')->with(['message' => 'Order Approve Success', 'type' => 'success']);
                }
                catch (\Exception $e) {
                    DB::rollback();
                    CommonHelpers::LogSystemError('Orders', $e->getMessage());
                    return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
                }  
            
            }
          
        }else if ($order->status == Statuses::FOR_SCHEDULE) {
            
            $request->validate([
                'schedule_date'     => 'required|date',
                'transaction_type'  => 'required|in:logistics,third party',
                'carrier_name' => 'required_if:transaction_type,third party',
                'delivery_reference' => 'required_if:transaction_type,third party'
            ]);
            try {

                Orders::where('id', $request->order_id)->update([
                    'status' => Statuses::FOR_DELIVERY,
                    'schedule_date' => $request->schedule_date,
                    'transaction_type' => $request->transaction_type,
                    'carrier_name' => $request->carrier_name,
                    'delivery_reference' => $request->delivery_reference,
                    'logistics_remarks' => $request->logistics_remarks,
                    'scheduled_by_logistics' => CommonHelpers::myId(),
                    'scheduled_at_logistics' => now(),
                ]);

                DB::commit();
                return redirect('/orders')->with(['message' => 'Order Schedule Successful', 'type' => 'success']);

            }
            catch (\Exception $e) {
                DB::rollback();
                CommonHelpers::LogSystemError('Orders', $e->getMessage());
                return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
            }

                
        }else if ($order->status == Statuses::FOR_DELIVERY){
            
            $request->validate([
                'proof_of_delivery'  => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            try{
                $timestamp = now()->timestamp;

                if ($request->hasFile('proof_of_delivery')) {
                    $file = $request->file('proof_of_delivery');
                    $filename = $timestamp . '_' . $file->getClientOriginalName();
                    $file->move(public_path('delivery/proof_of_delivery'), $filename);
                }        

                Orders::where('id', $request->order_id)->update([
                    'status' => Statuses::TO_CLOSE,
                    'proof_of_delivery' => $filename,
                    'scheduled_by_logistics' => CommonHelpers::myId(),
                    'scheduled_at_logistics' => now(),
                ]);

                self::deductReservation($order->id);
                    
                DB::commit();
                return redirect('/orders')->with(['message' => 'Order Delivery Success', 'type' => 'success']);
            }
            catch (\Exception $e) {
                DB::rollback();
                CommonHelpers::LogSystemError('Orders', $e->getMessage());
                return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
            }


        } else if ($order->status == Statuses::TO_CLOSE) {

            try{
                Orders::where('id', $request->order_id)->update([
                    'status' => Statuses::CLOSED,
                    'closed_by_ecomm' => CommonHelpers::myId(),
                    'closed_at_ecomm' => now(),
                ]);
                DB::commit();
                return redirect('/orders')->with(['message' => 'Order Closed Successfully', 'type' => 'success']);
            }
            catch (\Exception $e) {
                DB::rollback();
                CommonHelpers::LogSystemError('Orders', $e->getMessage());
                return back()->with(['message' => 'Order transaction failed', 'type' => 'error']);
            }
        }
       
    }

    public function deductReservation($orderId)
        {
        $reservations = ItemReservation::where('order_id', $orderId)
                        ->where('status', 'reserved')
                        ->get();
  
        foreach ($reservations as $reservation) {
            $item = ItemInventory::where('digits_code', $reservation->digits_code)->first();


            // Deduct from stock and reserved
            $item->qty -= $reservation->quantity;
            $item->reserved_qty -= $reservation->quantity;
            $item->save();

            // Update reservation status
            $reservation->status = 'deducted';
            $reservation->save();
        }

    }

    public function upload($encryptedId)
    {
        try {
            $orderId = Crypt::decryptString($encryptedId);
            $order = Orders::findOrFail($orderId);

            return view('uploader', compact('order', 'encryptedId'));

        } catch (DecryptException $e) {
            abort(404); 
        }
    }

    public function customerUploadFile(Request $request)
    {
        try {
            $orderId = Crypt::decryptString($request->encrypted_id);
            $order = Orders::findOrFail($orderId);

            if ($request->hasFile('payment_proof')) {
                $files = $request->file('payment_proof'); 
                $uploadedFilenames = [];
                
                $uploadPath = public_path('payment/uploaded-payment_proof');
                
                // Loop through each uploaded file
                foreach ($files as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $filename);
                    $uploadedFilenames[] = $filename;
                }
                
                if (empty($uploadedFilenames)) {
                    return response()->json(['success' => false, 'message' => 'No valid image files uploaded.'], 400);
                }
                
                // Implode filenames with comma separator
                $order->payment_proof = implode(',', $uploadedFilenames);
                $order->status = Statuses::FOR_VERIFICATION;
                $order->save();
                
               return response()->json(['success' => true, 'message' => 'File uploaded successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createDemTransaction($headerId){
        $headerDatas = Orders::find($headerId);
        $orderLinesDatas = OrderLines::where('order_id',$headerId)->get();
        $dem_order_id = DB::connection('dem')->table('order_header')->insertGetId([
            'order_no'                 => $headerDatas->reference_number,
            'status'                   => 4,
            'platform_name_id'         => 37,
            'billing_email'            => $headerDatas->email_address,
            'billing_phone'            => $headerDatas->contact_details,
            'shipping_address_line1'   => $headerDatas->delivery_address,
            'shipping_first_name'      => $headerDatas->first_name,
            'shipping_last_name'       => $headerDatas->last_name,
            'billing_full_name'        => $headerDatas->first_name ." ". $headerDatas->last_name,
            'shipping_full_name'       => $headerDatas->first_name ." ". $headerDatas->last_name,
            'created_by'               => 1000, //DEM Creator
		    'created_at'               => date('Y-m-d H:i:s'),
            'order_created'            => $headerDatas->created_at,
            'payment_method'           => $headerDatas->payment_method ?? null
        ]);

        foreach($orderLinesDatas ?? [] as $key => $val){
            $items = DB::connection('dem')->table('products')->where('digits_code',$val->digits_code)->first() ?? NULL;
            DB::connection('dem')->table('order_body')->insert([
                'items_id'         => $dem_order_id . $items->id,
                'header_id'        => $dem_order_id,
                'item_id'          => $items->id,
                'sku'              => $items->digits_code ?? $val->digits_code,
                'item_description' => $items->item_description,
                'quantity'         => $val->qty
            ]);
        }
    }

    public function export(){
        $filename = "Orders - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();

        return Excel::download(new OrdersExport($query), $filename . '.xlsx');
    }
}