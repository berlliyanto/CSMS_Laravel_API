<?php

namespace App\Exports;

use App\Models\Assestment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssestmentExport implements FromQuery, WithMapping, WithHeadings
{

    use Exportable;
    private $cleaner_id;

    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($cleaner_id)
    {
        $this->cleaner_id = $cleaner_id;
    }

    public function query()
    {
        if($this->cleaner_id) {
            return Assestment::query()->where('cleaner_id', $this->cleaner_id);
        }
        
        return Assestment::query();
    }

    public function headings(): array
    {
        return [
            "No",
            "Leader",
            "Cleaner",
            "Lokasi",
            "Perilaku",
            "Sikap",
            "Penampilan",
            "Tanggung Jawab",
            "Kompetensi",
            "Total"
        ];
    }

    public function map($assestment): array
    {
        return [
            ($assestment->id),
            ($assestment->leaders->name),
            ($assestment->cleaners->name),
            ($assestment->locations->location_name),
            ($assestment->plk_s + $assestment->plk_ddb),
            ($assestment->sik_mptu + $assestment->sik_ktp + $assestment->sik_kdtma + $assestment->sik_mw + $assestment->sik_rmtp),
            ($assestment->pnm_r + $assestment->pnm_mslc + $assestment->pnm_q),
            ($assestment->tj_ktw + $assestment->tj_kwdmp + $assestment->tj_kd + $assestment->tj_mpsj + $assestment->tj_mpmp),
            ($assestment->kom_k + $assestment->kom_p + $assestment->kom_kdb + $assestment->kom_ptp + $assestment->kom_kmk + $assestment->kom_s),
            ($assestment->plk_s + $assestment->plk_ddb + $assestment->sik_mptu + $assestment->sik_ktp + $assestment->sik_kdtma + $assestment->sik_mw + $assestment->sik_rmtp +
            $assestment->pnm_r + $assestment->pnm_mslc + $assestment->pnm_q + $assestment->tj_ktw + $assestment->tj_kwdmp + $assestment->tj_kd + $assestment->tj_mpsj + $assestment->tj_mpmp +
            $assestment->kom_k + $assestment->kom_p + $assestment->kom_kdb + $assestment->kom_ptp + $assestment->kom_kmk + $assestment->kom_s)
        ];
    }
}
