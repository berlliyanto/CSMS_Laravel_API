<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AssestmentController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CleanerController;
use App\Http\Controllers\CleaningController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TaskController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// php artisan serve --host 192.168.100.160 --port 8080

Route::middleware(['auth:sanctum'])->group(function () {
    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/update_password', [AuthController::class, 'updatePassword']);

    //Cleaners
    Route::get('/all_cleaners', [CleanerController::class, 'index']);

    //Area
    Route::get('/areas', [AreaController::class, 'index']);
    Route::get('/areas_by_location/{location_id}', [AreaController::class, 'areaByLocation']);

    //Location
    Route::get('/locations', [LocationController::class, 'index']);

    //Assestment
    Route::get('/assestments', [AssestmentController::class, 'index']);
    Route::post('/assestments', [AssestmentController::class, 'store']);
    Route::get('/calculate_assestment', [AssestmentController::class, 'calculateAssestments']);
    Route::get('/calculate_assestment_cleaner/{id}', [AssestmentController::class, 'calculateAssestmentsPerCleaner']);
    
    Route::get('/assestment_filter_date', [AssestmentController::class, 'filterByDate']);

    //Assign Task
    Route::get('/assigns', [AssignController::class, 'index']);
    Route::get('/assign/{id}', [AssignController::class, 'show']);
    Route::get('/index_assign_leader', [AssignController::class, 'indexByLeader'])->middleware('must.leader');
    Route::delete('/delete_assign_with_tasks/{id}', [AssignController::class, 'destroyAssignWithTasks'])->middleware('must.leader');
    Route::put('/update_assign_with_tasks/{id}', [AssignController::class, 'updateAssignWithTasks'])->middleware('must.leader');
    Route::put('/update_assign_task/{id}', [AssignController::class, 'updateTaskAssign'])->middleware('must.leader');

    Route::get('/index_assign_supervisor', [AssignController::class, 'indexBySupervisor'])->middleware('must.supervisor');
    Route::put('/update_assign_supervisor/{id}', [AssignController::class, 'updateBySupervisor'])->middleware('must.supervisor');

    Route::get('/index_assign_danone', [AssignController::class, 'indexByDanone'])->middleware('must.danone');
    Route::put('/update_assign_danone/{id}', [AssignController::class, 'updateByDanone'])->middleware('must.danone');

    Route::get('/assign_filter_date', [AssignController::class, 'filterByDate']);
    Route::get('/assign_count', [AssignController::class, 'countAssign']);
    Route::get('/assign_analytics', [AssignController::class, 'assignAnalytics']);
    
    //Task
    Route::post('/assign_task', [TaskController::class, 'storeTasksWithAssign'])->middleware('must.leader');
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/task/{id}', [TaskController::class, 'show']);
    Route::get('/tasks_by_cleaner', [TaskController::class, 'tasksByCleaner']);
    Route::get('/show_tasks_by_cleaner/{id}/{assignId}', [TaskController::class, 'showTasksByCleaner']);
    Route::put('/update_status_task/{id}', [TaskController::class, 'updateStatus']);
    Route::post('/update_finish_task/{id}', [TaskController::class, 'updateFinishTask']);
    Route::get('/task_count', [TaskController::class, 'countTask']);
});

//Image
Route::get('/images/{file}', [ImageController::class, 'show']);
Route::get('/download_image/{file}', [ImageController::class, 'downloadFile']);

//http://192.168.100.160:8080/api/assign_filter?type=daily&start_date=2023-12-16&end_date=2023-12-17

Route::post('/tes', [ImageController::class, 'tes']);

//Export Excel
Route::get('/assestments_export', [AssestmentController::class, 'exportAssestments']);
Route::get('/assign_export', [AssignController::class, 'assignExport']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
