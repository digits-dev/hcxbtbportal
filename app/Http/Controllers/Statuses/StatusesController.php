<?php

namespace App\Http\Controllers\Statuses;

use App\Exports\SubmasterExport;
use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Statuses;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class StatusesController extends Controller
{
    private $sortBy;
    private $sortDir;
    private $perPage;

    public function __construct() {
        $this->sortBy = request()->get('sortBy', 'statuses.id');
        $this->sortDir = request()->get('sortDir', 'desc');
        $this->perPage = request()->get('perPage', 10);
    }

    public function getAllData(){
        $query = Statuses::query()->with('getCreatedBy', 'getUpdatedBy');
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
        $data['tableName'] = 'statuses';
        $data['page_title'] = 'Statuses';
        $data['statuses'] = self::getAllData()->paginate($this->perPage)->withQueryString();
        $data['queryParams'] = request()->query();

        return Inertia::render("Statuses/Statuses", $data);
    }

    public function create(Request $request){

        $validatedFields = $request->validate([
            'name' => 'required|string|max:30|unique:statuses,name',
            'color' => 'required|string',
        ]);

        try {

            Statuses::create([
                'name' => $validatedFields['name'],   
                'color' => $validatedFields['color'], 
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
            'name' => 'required|string|max:30',
            'color' => 'required|string|max:30',
            'status' => 'required|string',
        ]);

        try {
    
                $statuses = Statuses::find($request->id);

                if (!$statuses) {
                    return back()->with(['message' => 'Status not found!', 'type' => 'error']);
                }
        
                $statusExist = Statuses::where('name', $request->name)->exists();

                if ($request->name !== $statuses->name) {
                    if (!$statusExist) {
                        $statuses->name = $validatedFields['name'];
                    } else {
                        return back()->withErrors(['name' => 'Status Name already exists!', 'type' => 'error']);
                    }
                }
        
                $statuses->name = $validatedFields['name'];
                $statuses->color = $validatedFields['color'];
                $statuses->status = $validatedFields['status'];
                $statuses->updated_by = CommonHelpers::myId();
                $statuses->updated_at = now();
        
                $statuses->save();


                return back()->with(['message' => 'Status Updating Success!', 'type' => 'success']);
            }  

        catch (\Exception $e) {

            CommonHelpers::LogSystemError('Statuses', $e->getMessage());
            return back()->with(['message' => 'Status Updating Failed!', 'type' => 'error']);
        }
    }


       public function export()
    {

        $headers = [
            'Status Name',
            'Color',
            'Status',
            'Created By',
            'Updated By',
            'Created Date',
            'Updated Date',
        ];

        $columns = [
            'name',
            'color',
            'status',
            'getCreatedBy.name',
            'getUpdatedBy.name',
            'created_at',
            'updated_at',
        ];

        $filename = "Statuses - " . date ('Y-m-d H:i:s');
        $query = self::getAllData();
        return Excel::download(new SubmasterExport($query, $headers, $columns), $filename . '.xlsx');

    }
}
