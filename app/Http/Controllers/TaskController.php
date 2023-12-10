<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskDetailResource;
use App\Http\Resources\TaskResource;
use App\Models\Assign;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with([
            'assign:id,assign_by,area_id,task,checked_supervisor_at,verified_danone_at',
            'cleaner:id,name',
            'assign.assignBy:id,name',
            'assign.area:id,area_name,location_id',
            'assign.area.location:id,location_name',
        ])->get();

        $message = 'Data retrieved successfully';

        $data = TaskResource::collection($tasks);

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 200);

    }

    public function show($id)
    {
        $tasks = Task::with([
            'assign:id,assign_by,area_id,task',
            'cleaner:id,name',
            'assign.assignBy:id,name',
            'assign.area:id,area_name,location_id',
            'assign.area.location:id,location_name',
        ])->where('id', $id)->get()->first();

        $message = 'Data retrieved successfully';

        $data = new TaskResource($tasks);

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public function tasksByCleaner()
    {
        $id = Auth::user()->id;

        $tasks = Task::with([
            'assign:id,assign_by,area_id,task',
            'cleaner:id,name',
            'assign.assignBy:id,name',
            'assign.area:id,area_name,location_id',
            'assign.area.location:id,location_name',
        ])->where('cleaner_id', $id)->get();

        $groupedTasks = $tasks->groupBy('status');

        $formattedData = [];
        foreach ($groupedTasks as $status => $task) {
            $formattedData[] = [
                'status' => $status,
                'count' => $task->count(),
                'tasks' => TaskResource::collection($task->take(10)),
            ];
        }

        $message = 'Data retrieved successfully';

        return response()->json([
            'message' => $message,
            'data' => $formattedData,
        ], 200);
    }

    public function showTasksByCleaner($id, $assignId)
    {

        $tasks = Task::where('id', $id)
            ->get()->first();

        if ($tasks == null) {
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }

        $data = new TaskResource($tasks->loadMissing([
            'assign:id,assign_by,area_id,task',
            'cleaner:id,name',
            'assign.assignBy:id,name',
            'assign.area:id,area_name,location_id',
            'assign.area.location:id,location_name',
        ]));

        $cleanerData = Task::with('cleaner:id,name')->where('assign_id', $assignId)->get();

        $cleanersList = [];

        foreach ($cleanerData as $cleaners) {
            $cleanersList[] = [
                'id' => $cleaners->cleaner->id,
                'name' => $cleaners->cleaner->name
            ];
        }

        $message = 'Data retrieved successfully';

        return response()->json([
            'message' => $message,
            'data' => $data,
            'cleaners' => $cleanersList
        ], 200);
    }

    public function storeTasksWithAssign(Request $request)
    {
        $request->validate([
            "area_id" => "required|exists:areas,id",
            "cleaners" => "required|array",
            "task" => "required",
        ]);

        DB::beginTransaction();

        try {

            $assignBy = Auth::user()->id;

            $assign = Assign::create([
                "assign_by" => $assignBy,
                "area_id" => $request->area_id,
                "task" => $request->task,
            ]);
            $assignId = $assign->id;

            foreach ($request->cleaners as $cleanerId => $value) {
                Task::create([
                    "assign_id" => $assignId,
                    "cleaner_id" => $value,
                    "status" => "Pending"
                ]);
            }

            DB::commit();

            return response()->json(["message" => "success"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            "status" => "required|in:Finish,Not Finish,Pending,On Progress",
        ]);

        $task = Task::where('id', $id);
        $updateTask = $task->update($request->only('status'));

        if ($task) {
            return response()->json(["message" => "success", "data" => $updateTask], 200);
        }
        return response()->json(["message" => "failed"], 500);
    }

    public function updateFinishTask(Request $request, $id)
    {
        $request->validate([
            "status" => "required|in:Finish,Not Finish",
        ]);

        if ($request->status == "Not Finish") {
            $request->validate([
                "alasan" => "required"
            ]);
        }

        $newImageBefore = null;
        $newImageProgress = null;
        $newImageFinish = null;

        if ($request->image_before && $request->image_finish && $request->image_progress) {
            $request->validate([
                "image_before" => "image|mimes:jpeg,png,jpg,gif|max:4048",
                "image_finish" => "image|mimes:jpeg,png,jpg,gif|max:4048",
                "image_progress" => "image|mimes:jpeg,png,jpg,gif|max:4048"
            ]);
            $imageBefore = $this->generateRandomString();
            $imageFinish = $this->generateRandomString();
            $imageProgress = $this->generateRandomString();

            $extensionBefore = $request->image_before->extension();
            $extensionFinish = $request->image_finish->extension();
            $extensionProgress = $request->image_progress->extension();

            $newImageBefore = $imageBefore . '.' . $extensionBefore;
            $newImageFinish = $imageFinish . '.' . $extensionFinish;
            $newImageProgress = $imageProgress . '.' . $extensionProgress;

            Storage::putFileAs('images', $request->image_before, $newImageBefore);
            Storage::putFileAs('images', $request->image_finish, $newImageFinish);
            Storage::putFileAs('images', $request->image_progress, $newImageProgress);
        }

        $task = Task::where('id', $id);
        $updateTask = $task->update([
            "image_before" => $newImageBefore,
            "image_finish" => $newImageFinish,
            "image_progress" => $newImageProgress,
            "status" => $request->status,
            "alasan" => $request->alasan,
            "catatan" => $request->catatan,
        ]);

        return response()->json(["message" => "success", "data" => $updateTask], 200);
    }

    function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
