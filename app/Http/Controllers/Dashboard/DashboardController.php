<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\AdmEmbeddedDashboard;
use App\Models\AdmEmbeddedDashboardPrivilege;
use App\Models\AdminCategory;
use App\Models\AdminClassification;
use App\Models\AdminItemMaster;
use App\Models\AdminItemMasterHistory;
use App\Models\AdminSubClassification;
use App\Models\AdmModels\AdmPrivileges;
use App\Models\AdmModels\AdmSettings;
use App\Models\Brands;
use App\Models\Classifications;
use App\Models\GashaponBrands;
use App\Models\GashaponCategories;
use App\Models\GashaponItemMaster;
use App\Models\GashaponItemMasterHistory;
use App\Models\GashaponProductTypes;
use App\Models\ItemMaster;
use App\Models\ItemMasterHistory;
use App\Models\Orders;
use App\Models\RmaCategories;
use App\Models\RmaClassifications;
use App\Models\RmaItemMaster;
use App\Models\RmaItemMasterHistory;
use App\Models\RmaSubClassifications;
use App\Models\ServiceCenterCategories;
use App\Models\ServiceCenterClassifications;
use App\Models\ServiceCenterItemMaster;
use App\Models\ServiceCenterItemMasterHistory;
use App\Models\ServiceCenterSubClassifications;
use App\Models\Statuses;
use App\Models\SubClassifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{

    public function getIndex(): Response
    {

        $data = [];

        $data['dashboard_settings_data'] = AdmSettings::whereIn('name', ['Default Dashboard', 'Embedded Dashboard'])
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->content => $item->content_input_type];
        })
        ->toArray();

        $dashboard_privilege = AdmEmbeddedDashboardPrivilege::where('adm_privileges_id',  CommonHelpers::myPrivilegeId())
                ->pluck('adm_embedded_dashboard_id');

        $data['embedded_dashboards'] = AdmEmbeddedDashboard::whereIn('id', $dashboard_privilege)
            ->where('status', 'ACTIVE')
            ->get();


        // FOR DASHBOARD

        $order = Orders::query();

        if (CommonHelpers::myPrivilegeId() == AdmPrivileges::HOMECREDITSTAFF){
            $order = $order->where('created_by', CommonHelpers::myId());
        }

        // FOR CHART
        // DAILY
        $daily = (clone $order)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->values();

        $dailyChart = [
            'data' => $daily->pluck('total')->toArray(),
            'labels' => $daily->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->format('M j'))
                ->toArray()
        ];

        // MONTHLY
        $monthly = (clone $order)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        $monthlyChart = [
            'data' => $monthly->pluck('total')->toArray(),
            'labels' => $monthly->pluck('month')
                ->map(fn($m) => Carbon::parse($m . '-01')->format('M Y'))
                ->toArray()
        ];

        // YEARLY
        $yearly = (clone $order)
            ->selectRaw('YEAR(created_at) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $yearlyChart = [
            'data' => $yearly->pluck('total')->toArray(),
            'labels' => $yearly->pluck('year')->toArray()
        ];

        $data['order_chart'] = [
            'daily' => $dailyChart,
            'monthly' => $monthlyChart,
            'yearly' => $yearlyChart
        ];


        // FOR STATUSES
        $data['statuses_count'] = [
            'total_orders'     => $order->count(),
            'for_payment'      => (clone $order)->where('status', Statuses::FOR_PAYMENT)->count(),
            'for_verification' => (clone $order)->where('status', Statuses::FOR_VERIFICATION)->count(),
            'for_processing'   => (clone $order)->where('status', Statuses::ORDER_PROCESSING)->count(),
            'incomplete'       => (clone $order)->where('status', Statuses::INCOMPLETE)->count(),
            'for_schedule'     => (clone $order)->where('status', Statuses::FOR_SCHEDULE)->count(),
            'for_delivery'     => (clone $order)->where('status', Statuses::FOR_DELIVERY)->count(),
            'to_close'         => (clone $order)->where('status', Statuses::TO_CLOSE)->count(),
            'closed'           => (clone $order)->where('status', Statuses::CLOSED)->count(),
        ];

        return Inertia::render('Dashboard/Dashboard', $data);
    }
}
