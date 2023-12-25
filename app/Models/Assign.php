<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assign_by', 'area_id',
        'task', 'supervisor_id',
        'checked_supervisor_at',
        'verified_danone_at',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latestNotDeleted = self::orderBy('id', 'desc')->withTrashed()->first();

            if ($latestNotDeleted) {
                $latestId = $latestNotDeleted->id;
            } else {
                $latestId = 0;
            }

            $model->code_cs = 'CS' . str_pad($latestId + 1, 9, '0', STR_PAD_LEFT);
        });
    }

    public function assignBy()
    {
        return $this->belongsTo(User::class, 'assign_by', 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assign_id', 'id');
    }
}
