<?php

namespace App\Http\Controllers\Orders;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\OrderLines;
use App\Models\ItemMaster;
use App\Models\Statuses;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\OrderConfirmationMail;
use App\Mail\SendProofOfPaymentLink;
use App\Mail\ReSendProofOfPaymentLink;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

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
        $query = Orders::query()->with(['getStatus', 'getCreatedBy', 'getUpdatedBy']);
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
        ->select('digits_code', 'item_description', 'model','actual_color', 'size' )->get();
 
        return Inertia::render("Orders/CreateOrderForm", $data);
    }


    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'delivery_address'   => 'required|string|max:255',
            'email_address'      => 'required|email|max:255',
            'contact_details'    => 'required|string|max:50',
            'financed_amount'    => 'required|numeric|min:0',
            'has_downpayment'    => 'required|in:yes,no',
            'downpayment_value'  => 'nullable|numeric|min:0',
            'approved_contract'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'items'              => 'required|array|min:1',

        ]);

        $timestamp = now()->timestamp;

        // Handle file upload
        if ($request->hasFile('approved_contract')) {
            $file = $request->file('approved_contract');
            $filename = $timestamp . '_' . $file->getClientOriginalName();
            $file->move(public_path('contract/uploaded-contract'), $filename);
        }

        if ($validatedData['has_downpayment'] === 'yes') {
            $status = Statuses::FOR_VERIFICATION;
        } else {
            $status =  Statuses::CONFIRMED;
        };

        // Prepare order data
        $orderData = [
            'reference_number'   => Orders::generateReferenceNumber(),
            'status'             => $status,
            'customer_name'      => $validatedData['customer_name'],
            'delivery_address'   => $validatedData['delivery_address'],
            'email_address'      => $validatedData['email_address'],
            'contact_details'    => $validatedData['contact_details'],
            'financed_amount'    => $validatedData['financed_amount'],
            'has_downpayment'    => $validatedData['has_downpayment'],
            'downpayment_value'  => $validatedData['downpayment_value'],
            'approved_contract'  => $filename ?? null,
            'order_date'         => now(),
        ];

        // Create order
        $order = Orders::create($orderData);
        $orderId = $order->id;

        // Prepare order lines
        $lines = [];
        foreach ($validatedData['items'] as $item) {
            $lines[] = [
                'order_id'    => $orderId,
                'digits_code' => $item['digits_code'],
                'qty'         => $item['quantity'],
                'created_at'  => now(),
            ];
        }
        OrderLines::insert($lines);

        // Prepare and send email
        $encryptedId = Crypt::encryptString($orderId);

        // if ($validatedData['has_downpayment'] === 'yes') {
        //     Mail::to($validatedData['email_address'])->send(new SendProofOfPaymentLink([
        //         'customer_name' => $validatedData['customer_name'],
        //         'payment_link'  => url('/upload/' . $encryptedId),
        //     ]));
        // } else {
        //     Mail::to($validatedData['email_address'])->send(new OrderConfirmationMail($orderData));
        // }

        

        return redirect('/orders');
    }

    public function view ($id) {
        $data = [];
        $data['page_title'] = ' Order Details';
        $data['order'] = Orders::where('id',$id)->first();
        $data['lines'] = OrderLines::leftJoin('item_masters', 'item_masters.digits_code', 'order_lines.digits_code')
        ->where('order_id', $id)->get();
 
        return Inertia::render("Orders/ViewOrderDetails", $data);
    } 

    public function update($id) {
        
        $data = [];
        $data['order'] = Orders::where('id',$id)->first();
        $data['lines'] = OrderLines::leftJoin('item_masters', 'item_masters.digits_code', 'order_lines.digits_code')
        ->where('order_id', $id)->get();
 
        return Inertia::render("Orders/AccountingVerification", $data);
    }

    public function updateSave(Request $request) {
        $timestamp = now()->timestamp;
        if ($request->hasFile('dp_receipt')) {
            $file = $request->file('dp_receipt');
            $filename = $timestamp . '_' . $file->getClientOriginalName();
            $file->move(public_path('dp-receipt/uploaded-receipt'), $filename);
        }
    
        $order = Orders::where('id', $request->order_id)->first();

 
        $encryptedId = Crypt::encryptString($order->id);
        
        if ($request->action == 'reject') {
            Orders::where('id', $request->order_id)->update([
                'status' => Statuses::REJECTED,
                // 'rejected_by' => CommonHelpers::myId(),
                // 'rejected_at' => now(),
            ]);

            Mail::to($order->email_address)->send(new ReSendProofOfPaymentLink([
                'customer_name' => $order->customer_name,
                'payment_link' => url('/upload/' . $encryptedId),
            ]));
        }else {
            Orders::where('id', $request->order_id)->update([
                'status' => Statuses::CONFIRMED,
                'dp_receipt' => $filename,
                'verified_by_acctg' => CommonHelpers::myId(),
                'verified_at_acctg' => now(),
            ]);
        }
            
      return redirect('/orders');
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
                $existingFilenames = $order->payment_proof ? explode(',', $order->payment_proof) : [];
                $allFilenames = array_merge($existingFilenames, $uploadedFilenames);

                $order->payment_proof = implode(',', $allFilenames);
                $order->status = Statuses::FOR_VERIFICATION;
                $order->save();

                
               return response()->json(['success' => true, 'message' => 'File uploaded successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed.', 'error' => $e->getMessage()], 500);
        }
    }

}