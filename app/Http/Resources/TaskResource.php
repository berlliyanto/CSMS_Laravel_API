<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tasks = str_contains($this->assign->task, "|") ? explode("|", $this->assign->task) : [$this->assign->task];

        return [
            "id" => $this->id,
            "cleaner_id" => $this->cleaner_id,
            "assign_id" => $this->assign_id,
            "tasks" => $tasks,
            "image_before" => $this->image_before,
            "image_progress" => $this->image_progress,
            "image_finish" => $this->image_finish,
            "status" => $this->status,
            "alasan" => $this->alasan,
            "catatan" => $this->catatan,
            "created_at" => date_format($this->created_at, "Y-m-d H:i:s"),
            "updated_at" => date_format($this->updated_at, "Y-m-d H:i:s"),
            "assign" => $this->whenLoaded('assign', function(){
                return [
                    "id" => $this->assign->id,
                    "assign_by" => $this->assign->assignBy,
                    "area" => [
                        "id" => $this->assign->area->id,
                        "area_name" => $this->assign->area->area_name
                    ],
                    "location" => [
                        "id" => $this->assign->area->location->id,
                        "location_name" => $this->assign->area->location->location_name
                    ]
                ];
            }),
            "cleaner" => $this->whenLoaded('cleaner')
        ];
    }
}
