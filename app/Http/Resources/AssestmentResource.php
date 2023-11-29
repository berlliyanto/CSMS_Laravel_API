<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssestmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id" => $this->id,
            "leader" => $this->whenLoaded('leaders'),
            "cleaner" => $this->whenLoaded('cleaners'),
            "location" => $this->whenLoaded('locations'),
            "perilaku" => $this->plk_s + $this->plk_ddb,
            "sikap" => $this->sik_mptu + $this->sik_ktp + $this->sik_kdtma + $this->sik_mw + $this->sik_rmtp,
            "penampilan" => $this->pnm_r + $this->pnm_mslc + $this->pnm_q,
            "tanggung_jawab" => $this->tj_ktw + $this->tj_kwdmp + $this->tj_kd + $this->tj_mpsj + $this->tj_mpmp,
            "kompetensi" => $this->kom_k + $this->kom_p + $this->kom_kdb + $this->kom_ptp + $this->kom_kmk + $this->kom_s,
            "total" => $this->plk_s + $this->plk_ddb + $this->sik_mptu + $this->sik_ktp + $this->sik_kdtma + $this->sik_mw + $this->sik_rmtp +
                $this->pnm_r + $this->pnm_mslc + $this->pnm_q + $this->tj_ktw + $this->tj_kwdmp + $this->tj_kd + $this->tj_mpsj + $this->tj_mpmp +
                $this->kom_k + $this->kom_p + $this->kom_kdb + $this->kom_ptp + $this->kom_kmk + $this->kom_s,
            "created_at" => date_format($this->created_at, "Y-m-d H:i:s"),
            "updated_at" => date_format($this->updated_at, "Y-m-d H:i:s"),
        ];
    }
}
