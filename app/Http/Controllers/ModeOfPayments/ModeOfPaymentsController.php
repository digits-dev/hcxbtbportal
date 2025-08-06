<?php

namespace App\Http\Controllers\ModeOfPayments;

use App\Exports\SubmasterExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\ModeOfPayments;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ModeOfPaymentsController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'mode_of_payments.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

    public function getAllData(){
        $query = ModeOfPayments::query()->with('getCreatedBy', 'getUpdatedBy');
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
        $data['tableName'] = 'mode_of_payments';
        $data['page_title'] = 'Mode of Payments';
        $data['mode_of_payments'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("ModeOfPayments/ModeOfPayments", $data);
    }

     public function create(Request $request){

        $validatedFields = $request->validate([
            'payment_name' => 'required|string|max:30|unique:mode_of_payments,payment_name',
        ]);

        try {

            ModeOfPayments::create([
                'payment_name' => $validatedFields['payment_name'],   
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => CommonHelpers::myId(),
            ]);
            
            return back()->with(['message' => 'Status Creation Success!', 'type' => 'success']);

        }

        catch (\Exception $e) {
            CommonHelpers::LogSystemError('Statuses', $e->getMessage());
            return back()->with(['message' => 'Status Creation Failed!', 'type' => 'error']);
        }
        
    }

    public function update(Request $request){

        $validatedFields = $request->validate([
            'payment_name' => 'required|string|max:30',
            'status' => 'required|string',
        ]);

        try {
    
                $mode_of_payment = ModeOfPayments::find($request->id);

                if (!$mode_of_payment) {
                    return back()->with(['message' => 'Payment not found!', 'type' => 'error']);
                }
        
                $statusExist = ModeOfPayments::where('payment_name', $request->payment_name)->exists();

                if ($request->payment_name !== $mode_of_payment->payment_name) {
                    if (!$statusExist) {
                        $mode_of_payment->payment_name = $validatedFields['payment_name'];
                    } else {
                        return back()->withErrors(['payment_name' => 'Payment Name already exists!', 'type' => 'error']);
                    }
                }
        
                $mode_of_payment->payment_name = $validatedFields['payment_name'];
                $mode_of_payment->status = $validatedFields['status'];
                $mode_of_payment->updated_by = CommonHelpers::myId();
                $mode_of_payment->updated_at = now();
        
                $mode_of_payment->save();


                return back()->with(['message' => 'Mode of Payment Updating Success!', 'type' => 'success']);
            }  

        catch (\Exception $e) {

            CommonHelpers::LogSystemError('Mode of Payments', $e->getMessage());
            return back()->with(['message' => 'Mode of Payment Updating Failed!', 'type' => 'error']);
        }
    }

    public function export()
    {

        $headers = [
            'Payment Name',
            'Status',
            'Created By',
            'Updated By',
            'Created Date',
            'Updated Date',
        ];

        $columns = [
            'payment_name',
            'status',
            'getCreatedBy.name',
            'getUpdatedBy.name',
            'created_at',
            'updated_at',
        ];

        $filename = "Mode of Payments - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();
        return Excel::download(new SubmasterExport($query, $headers, $columns), $filename . '.xlsx');

    }
}
