<?php

namespace App\Http\Controllers\Deliveries;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Deliveries;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use DB;

class DeliveriesController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'deliveries.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

    public function getAllData(){
        $query = Deliveries::query();
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
        $data['tableName'] = 'deliveries';
        $data['page_title'] = 'Deliveries';
        $data['deliveries'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("Deliveries/Deliveries", $data);
    }

    public function deliveryDetails($id){

        $data = [];
        $data['page_title'] = 'Delivery Details';
        $data['delivery_details'] = Deliveries::with('getLines.getItem')->find($id);

        return Inertia::render("Deliveries/DeliveryDetails", $data);
    }
}
