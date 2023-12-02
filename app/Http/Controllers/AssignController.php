<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignResource;
use App\Models\Assign;
use App\Models\Task;
use Carbon\Carbon;
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
        ])->get();

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

    public function assignByLeader()
    {
        $id = Auth::user()->id;

        $assigns = Assign::with([
            'assignBy:id,name',
            'area:id,area_name,location_id',
            'area.location:id,location_name',
            'tasks'
        ])->where('assign_by', $id)->get();

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

            $updateAssign = Assign::where('id', $id)->update([
                'area_id' => $request->area_id,
                'tasks' => $request->tasks
            ]);

            foreach ($request->cleaners as $key => $value) {
                Task::where('cleaner_id', $id)->update([
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
                    'message' => 'Tugas belum selesai atau tidak valid, tidak dapat di verifikasi'
                ], 200);
            }

            $updateAssign->update([
                'supervisor_id' => $idSupervisor,
                'checked_supervisor_at' => $time
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



    public function updateByDanone(Request $request, $id)
    {
        $request->validate([
            'isVerified' => 'required|boolean'
        ]);

        if ($request->isVerified == true) {
            $time = Carbon::now();
            $updateAssign = Assign::findOrFail($id);

            if($updateAssign->supervisor_id == null){
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
}
