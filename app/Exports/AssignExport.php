<?php

namespace App\Exports;

use App\Models\Assign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AssignExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    private $id, $from, $to;

    public function __construct($id, $from, $to)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
    }

    public function query()
    {
        if($this->id) {
            return Assign::with(['assignBy', 'area', 'area.location', 'tasks'])->where('id', $this->id);
        }

        return Assign::with(['assignBy', 'area', 'area.location', 'tasks'])->whereBetween('created_at', [$this->from, $this->to]);
    }

    public function headings(): array
    {
        return [
            "Kode Tugas",
            "Leader",
            "Cleaner",
            "Area",
            "Lokasi",
            "Tugas",
            "Status",
            "Di Cek Supervisor",
            "Di Verifikasi Danone",
            "Tanggal Dibuat",
        ];
    }

    public function map($assign): array
    {
        $cleaners = "";
        foreach ($assign->tasks as $task) {
            $cleaners .= $task->cleaner->name . ", ";
        }

        $status = $this->cekStatus($assign) ? 'Selesai' : 'Belum Selesai';
        $oldTask = $assign->task;
        $newTask = str_replace("|", ", ", $oldTask);

        return [
            ($assign->code_cs),
            ($assign->assignBy->name),
            ($cleaners),
            ($assign->area->area_name),
            ($assign->area->location->location_name),
            ($newTask),
            ($status),
            ($assign->checked_supervisor_at),
            ($assign->verified_andone_at),
            ($assign->created_at),
        ];
    }

    function cekStatus($assign): bool
    {
        return $assign->tasks->every(function ($task) {
            return in_array($task->status, ['Finish', 'Not Finish']);
        });
    }
}
