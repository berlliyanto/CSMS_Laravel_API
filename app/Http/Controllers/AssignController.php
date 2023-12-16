<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignResource;
use App\Models\Assign;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignController extends Controller
{
    public function index()
    {
        $assigns = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'supervisor:id,name',
            'tasks'
        ])->orderBy('id', 'desc')->get();

        return AssignResource::collection($assigns)->additional([
            'success' => true,
            'message' => 'Data fetched successfully',
        ]);
    }

    public function show($id)
    {
        $assign = Assign::where('id', $id)->get()->first();

        if (!$assign) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return new AssignResource($assign->loadMissing([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'supervisor:id,name',
            'tasks'
        ]));
    }

    public function indexByLeader()
    {
        $id = Auth::user()->id;

        $assigns = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'area.location:id,location_name',
            'supervisor:id,name',
            'tasks'
        ])->where('assign_by', $id)
            ->whereNull('supervisor_id')
            ->orderBy('id', 'desc')
            ->get();

        $data = AssignResource::collection($assigns);

        return response()->json([
            'message' => 'Data fetched successfully',
            'data' => $data
        ]);
    }

    public function updateAssignWithTasks(Request $request, $id)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'tasks' => 'required',
            'cleaners' => 'required|array',
            'cleaners.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();

        try {

            $updateAssign = Assign::where('id', $id)
                ->whereNotNull('supervisor_id')
                ->update([
                    'area_id' => $request->area_id,
                    'tasks' => $request->tasks
                ]);

            foreach ($request->cleaners as $key => $value) {
                Task::where('cleaner_id', $id)
                    ->whereNotIn('status', ['Finish', 'Not Finish'])
                    ->update([
                        'cleaner_id' => $value
                    ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data updated successfully',
                'data' => $updateAssign
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function indexBySupervisor()
    {
        $assigns = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'area.location:id,location_name',
            'supervisor:id,name',
            'tasks'
        ])
            ->whereNull('supervisor_id')
            ->orderBy('id', 'desc')
            ->get();

        return AssignResource::collection($assigns)->additional([
            'success' => true,
            'message' => 'Data fetched successfully',
        ]);
    }

    public function updateBySupervisor(Request $request, $id)
    {
        $request->validate([
            'isVerified' => 'required|boolean'
        ]);

        $idSupervisor = Auth::user()->id;

        if ($request->isVerified == true) {
            $time = Carbon::now();
            $updateAssign = Assign::with('tasks')->findOrFail($id);

            $validStatus = $updateAssign->tasks->every(function ($task) {
                return in_array($task->status, ['Finish', 'Not Finish']);
            });

            if (!$validStatus) {
                return response()->json([
                    'message' => 'Tugas yang diberi leader kepada setiap cleaner dalam 1 grup belum selesai semua'
                ], 401);
            }

            $updateAssign->update([
                'supervisor_id' => $idSupervisor,
                'checked_supervisor_at' => $time
            ]);

            return response()->json([
                'message' => 'Data updated successfully',
                'data' => $updateAssign
            ], 200);
        } else {
            return response()->json([
                'message' => 'Belum di verifikasi'
            ], 401);
        }
    }



    public function updateByDanone(Request $request, $id)
    {
        $request->validate([
            'isVerified' => 'required|boolean'
        ]);

        if ($request->isVerified == true) {
            $time = Carbon::now();
            $updateAssign = Assign::findOrFail($id);

            if ($updateAssign->supervisor_id == null) {
                return response()->json([
                    'message' => 'Belum ada supervisor'
                ], 401);
            }

            $updateAssign->update([
                'verified_danone_at' => $time
            ]);

            return response()->json([
                'message' => 'Data updated successfully',
                'data' => $updateAssign
            ]);
        } else {
            return response()->json([
                'message' => 'Belum di verifikasi'
            ], 200);
        }
    }

    public function destroyAssignWithTasks($id)
    {
        DB::beginTransaction();

        try {
            Assign::where('id', $id)->delete();
            Task::where('assign_id', $id)->delete();
            DB::commit();

            return response()->json([
                'message' => 'Data deleted successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function filterByDate(Request $request)
    {
        $dateType = $request->query('type'); // daily, monthly, yearly
        $startDate = $request->query('start_date'); // start date for filtering
        $endDate = $request->query('end_date'); // end date for filtering

        $tasks = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'area.location:id,location_name',
            'supervisor:id,name',
            'tasks'
        ])->when($dateType === 'daily', function ($query) use ($startDate) {
                return $query->whereDate('created_at', $startDate);
            })
            ->when($dateType === 'monthly', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($dateType === 'yearly', function ($query) use ($startDate) {
                return $query->whereYear('created_at', $startDate);
            })
            ->orderBy('id', 'desc')->get();

        $taskResource = AssignResource::collection($tasks)->additional([
            'success' => true,
            'message' => 'Data fetched successfully',
        ]);

        return $taskResource;
    }

    public function countAssign()
    {
        $total = Assign::count();
        $totalFinish = Assign::whereNotNull('verified_danone_at')->count();
        $totalNotFinish = Assign::whereNull('verified_danone_at')->count();
        return response()->json([
            'message' => 'Data fetched successfully',
            'data' => [
                "total" => $total,
                "total_finish" => $totalFinish,
                "total_not_finish" => $totalNotFinish
            ]
        ], 200);
    }
}
