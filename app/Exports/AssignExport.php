<?php

namespace App\Exports;

use App\Models\Assign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Builder;

class AssignExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */

    private $id, $from, $to, $location, $tipe;

    public function __construct($id, $from, $to, $location, $tipe)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->location = $location;
        $this->tipe = $tipe;
    }

    public function query()
    {
        if ($this->id) {
            $assigns =  Assign::with(['assignBy', 'area', 'area.location', 'tasks'])->where('id', $this->id);
            return $assigns;
        }

        if ($this->location) {
            $query = Assign::with(['assignBy', 'area', 'area.location', 'tasks'])
                ->whereHas('area', function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('id', $this->location);
                    });
                });
            if ($this->tipe === 'Harian') {
                $query->whereDate('created_at', $this->from);
            } elseif ($this->tipe === 'Bulanan') {
                $endDateTime = \DateTime::createFromFormat('Y-m-d', $this->to);
                $endDateTime->setTime(23, 59, 59);
                $endDate = $endDateTime->format('Y-m-d H:i:s');
                $query->whereBetween('created_at', [$this->from, $endDate]);
            } elseif ($this->tipe === 'Tahunan') {
                $query->whereYear('created_at', $this->from);
            }
            
            return $query->orderBy('id', 'desc');
        }

        $queryDefault = Assign::with(['assignBy', 'area', 'area.location', 'tasks']);
        if ($this->tipe === 'Harian') {
            $queryDefault->whereDate('created_at', $this->from);
        } elseif ($this->tipe === 'Bulanan') {
            $queryDefault->whereBetween('created_at', [$this->from, $this->to]);
        } elseif ($this->tipe === 'Tahunan') {
            $queryDefault->whereYear('created_at', $this->from);
        }

        return $queryDefault->orderBy('id', 'desc');
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
