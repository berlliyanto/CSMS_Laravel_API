<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assestment extends Model
{

    use HasFactory;

    protected $table = "assestments";

    protected $fillable = [
        'leader', 'cleaner', 'location', 'plk_s', 'plk_ddb',
        'sik_mptu', 'sik_ktp', 'sik_kdtma', 'sik_mw', 'sik_rmtp',
        'pnm_r', 'pnm_mslc', 'pnm_q',
        'tj_ktw', 'tj_kwdmp', 'tj_kd', 'tj_mpsj', 'tj_mpmp',
        'kom_k', 'kom_p', 'kom_kdb', 'kom_ptp', 'kom_kmk', 'kom_s'
    ];

    public function leaders()
    {
        return $this->belongsTo(User::class, 'leader', 'id');
    }

    public function cleaners()
    {
        return $this->belongsTo(User::class, 'cleaner', 'id');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'location', 'id');
    }
}
