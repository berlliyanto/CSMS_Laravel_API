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
            "tasks_detail" => $this->whenLoaded('tasks'),
            "supervisor_id" => $this->whenLoaded('supervisor'),
            "checked_supervisor_at" => $this->checked_supervisor_at,
            "checked_danone_at" => $this->checked_danone_at,
            "created_at" => date_format($this->created_at, "Y-m-d H:i:s"),
            "updated_at" => date_format($this->updated_at, "Y-m-d H:i:s"),
        ];
    }
}
