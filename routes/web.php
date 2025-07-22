<?php

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Admin\AdminApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\MenusController;
use App\Http\Controllers\Admin\ModulsController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\PrivilegesController;
use App\Http\Controllers\Admin\AnnouncementsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\AdmRequestController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\SystemErrorLogsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Users\ChangePasswordController;
use App\Http\Controllers\Users\ProfilePageController;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index']);
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::get('/reset_password', [ResetPasswordController::class, 'getIndex'])->name('reset_password');
Route::post('/send_resetpass_email', [ResetPasswordController::class, 'sendResetPasswordInstructions']);
Route::get('/reset_password_email/{email}', [ResetPasswordController::class, 'getResetIndex'])->name('reset_password_email');
Route::post('/send_resetpass_email/reset', [ResetPasswordController::class, 'resetPassword']);
Route::post('post_login', [LoginController::class, 'authenticate'])->name('post_login');
Route::get('/appname', [SettingsController::class, 'getAppname'])->name('app-name');
Route::get('/applogo', [SettingsController::class, 'getApplogo'])->name('app-logo');
Route::get('/login-details', [SettingsController::class, 'getLoginDetails'])->name('app-login-details');

Route::group(['middleware' => ['auth', 'web']], function () {

    //ANNOUNCEMENT
    Route::prefix('announcements')->group(function () {
        Route::get('/add_announcement', [AnnouncementsController::class, 'addAnnouncementForm']);
        Route::get('/edit_announcement/{id}', [AnnouncementsController::class, 'editAnnouncementForm']);
        Route::post('/create_announcement', [AnnouncementsController::class, 'createAnnouncement']);
        Route::post('/update_announcement', [AnnouncementsController::class, 'updateAnnouncement']);
        Route::post('/update_announcement_isread', [AnnouncementsController::class, 'updateAnnouncementIsread']);
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/sidebar', [MenusController::class, 'sidebarMenu'])->name('sidebar');

    //USERS
    Route::prefix('users')->group(function () {
        Route::post('/bulk_action', [AdminUsersController::class, 'bulkActions']);
        Route::post('/create', [AdminUsersController::class, 'create']);
        Route::post('/update', [AdminUsersController::class, 'update']);
        Route::get('/export', [AdminUsersController::class, 'export']);
    });


    //PROFILE PAGE
    Route::get('/profile', [ProfilePageController::class, 'getIndex'])->name('profile_page');
    Route::get('/edit_profile', [ProfilePageController::class, 'getEditProfile']);
    Route::post('/update_profile', [ProfilePageController::class, 'updateProfile']);
    Route::post('/update-theme', [ProfilePageController::class, 'updateTheme'])->name('update-theme');

    //CHANGE PASSWORD

    Route::prefix('change_password')->group(function () {
        Route::get('/', [ChangePasswordController::class, 'getIndex'])->name('change_password');
        Route::post('/update', [ChangePasswordController::class, 'changePassword'])->name('changePassword');
        Route::post('/waive', [ChangePasswordController::class, 'waive']);
    });

    //PRIVILEGES
    Route::prefix('privileges')->group(function () {
        Route::get('/create-privileges', [PrivilegesController::class, 'createPrivilegesView']);
        Route::get('/edit-privileges/{id}', [PrivilegesController::class, 'editPrivilegeView']);
        Route::post('/edit_save', [PrivilegesController::class, 'editPrivilege']);
        Route::post('/create_save', [PrivilegesController::class, 'createPrivilege']);
        Route::get('/export', [PrivilegesController::class, 'export']);
    });
   

    //MODULES
    Route::prefix('module_generator')->group(function () {
        Route::post('/create_module', [ModulsController::class, 'createModule']);
    });
   
  
    //MENUS
    Route::prefix('menu_management')->group(function () {
        Route::post('/create_menu', [MenusController::class, 'createMenu']);
        Route::post('/update_menu', [MenusController::class, 'updateMenu']);
        Route::post('/auto_update_menu', [MenusController::class, 'autoUpdateMenu']);
        Route::get('/edit/{menu}', [MenusController::class, 'editMenu']);
    });

    //APP SETTINGS
    Route::prefix('settings')->group(function () {
        Route::post('/add_embedded_dashboard', [SettingsController::class, 'addEmbeddedDashboard']);
        Route::post('/update_embedded_dashboard', [SettingsController::class, 'updateEmbeddedDashboard']);
        Route::post('/update_default_dashboard', [SettingsController::class, 'updateDefaultDashboard']);
        Route::post('/update_embedded_dashboard_button', [SettingsController::class, 'updateEmbedDashboardButton']);
    });

    //NOTIFICATION
    Route::prefix('notifications')->group(function () {
        Route::post('/read', [NotificationsController::class, 'markAsRead']);
        Route::post('/read_all', [NotificationsController::class, 'markAllAsRead']);
        Route::get('/view/{id}', [NotificationsController::class, 'viewNotification']);
        Route::get('/view_all', [NotificationsController::class, 'viewAllNotification']);
    });

    //FILTER
    Route::get('/filter/privileges', [AdmRequestController::class, 'privilegesFilter'])->name('privileges-filter');
    Route::get('/filter/users', [AdmRequestController::class, 'usersFilter'])->name('users-filter');

    //EXPORT
    Route::post('/request/export', [AdmRequestController::class, 'export'])->name('export');

    //SYSTEM ERROR LOGS
    Route::prefix('system_error_logs')->group(function () {
        Route::get('/export', [SystemErrorLogsController::class, 'export']);
    });

    // LOG USER ACCESS
    Route::prefix('logs')->group(function () {
        Route::get('/export', [LogsController::class, 'export']);
    });

    // API GENERATOR
    Route::prefix('api_generator')->group(function () {
        
        //API Requests
        Route::post('/generate_key', [AdminApiController::class, 'createKey']);

        //API Key Generation
        Route::post('/deactivate_key/{id}', [AdminApiController::class, 'deactivateKey']);
        Route::post('/activate_key/{id}', [AdminApiController::class, 'activateKey']);
        Route::post('/delete_key/{id}', [AdminApiController::class, 'deleteKey']);

        //API Create Generation

        Route::get('/create_api_view', [AdminApiController::class, 'createApiView']);
        Route::post('/create_api', [AdminApiController::class, 'createApi']);
        
        //API Edit
        Route::get('/edit/{id}', [AdminApiController::class, 'editApi']);
        Route::post('/update_api', [AdminApiController::class, 'updateApi']);

        // VIEW API
        Route::get('/view/{id}', [AdminApiController::class, 'viewApi']);

        // BULK ACTIONS
        Route::post('/bulk_action', [AdminApiController::class, 'bulkActions']);
        
       

    });

});

Route::group([
    'middleware' => ['auth', 'check.user'],
    'prefix' => config('adm_url.ADMIN_PATH'),
    'namespace' => 'App\Http\Controllers',
], function () {

    // Todo: change table
    $modules = [];
    try {
        $modules = DB::table('adm_modules')->whereIn('controller', CommonHelpers::getOthersControllerFiles())->get();
    } catch (\Exception $e) {
        Log::error("Load adm moduls is failed. Caused = " . $e->getMessage());
    }

    foreach ($modules as $v) {
        if (@$v->path && @$v->controller) {
            try {
                CommonHelpers::routeOtherController($v->path, $v->controller, 'app\Http\Controllers');
            } catch (\Exception $e) {
                Log::error("Path = " . $v->path . "\nController = " . $v->controller . "\nError = " . $e->getMessage());
            }
        }
    }
})->middleware('auth');

//ADMIN ROUTE
Route::group([
    'middleware' => ['auth', 'check.user'],
    'prefix' => config('ad_url.ADMIN_PATH'),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {

    // Todo: change table
    if (request()->is(config('ad_url.ADMIN_PATH'))) {
        $menus = DB::table('adm_menuses')->where('is_dashboard', 1)->first();
        if ($menus) {
            Route::get('/', 'Dashboard\DashboardContentGetIndex');
        } else {
            CommonHelpers::routeController('/', 'AdminController', 'App\Http\Controllers\Admin');
        }
    }

    // Todo: change table
    $modules = [];
    try {
        $modules = DB::table('adm_modules')->whereIn('controller', CommonHelpers::getMainControllerFiles())->get();
    } catch (\Exception $e) {
        Log::error("Load ad moduls is failed. Caused = " . $e->getMessage());
    }

    foreach ($modules as $v) {
        if (@$v->path && @$v->controller) {
            try {
                CommonHelpers::routeController($v->path, $v->controller, 'app\Http\Controllers\Admin');
            } catch (\Exception $e) {
                Log::error("Path = " . $v->path . "\nController = " . $v->controller . "\nError = " . $e->getMessage());
            }
        }
    }
})->middleware('auth');

