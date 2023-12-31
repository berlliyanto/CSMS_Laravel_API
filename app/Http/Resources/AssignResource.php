<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tasks = str_contains($this->task, "|") ? explode("|", $this->task) : [$this->task];

        return [
            "id" => $this->id,
            "code_cs" => $this->code_cs,
            "assign_by" => $this->whenLoaded('assignBy'),
            "area" => $this->whenLoaded("area", function () {
                return [
                    "id" => $this->area->id,
                    "area_name" => $this->area->area_name,
                ];
            }),
            "location" => $this->whenLoaded("area", function(){
                return [
                    "id" => $this->area->location->id,
                    "location_name" => $this->area->location->location_name
                ];
            }),
            "tasks" => $tasks,
            "tasks_detail" => $this->whenLoaded('tasks', function(){
                return collect($this->tasks)->each(function($task){
                    $task->cleaner;
                    return $task;
                });
            }),
            "status" => $this->whenLoaded('tasks', function(){
                return $this->tasks->every(function ($task) {
                    return in_array($task->status, ['Finish', 'Not Finish']);
                });
            }),
            "supervisor_id" => $this->whenLoaded('supervisor'),
            "checked_supervisor_at" => $this->checked_supervisor_at,
            "verified_danone_at" => $this->verified_danone_at,
            "created_at" => date_format($this->created_at, "Y-m-d H:i:s"),
            "updated_at" => date_format($this->updated_at, "Y-m-d H:i:s"),
        ];
    }
}
