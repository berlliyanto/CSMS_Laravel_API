<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleaner_id',
        'assign_id',
        'image_before',
        'image_progress',
        'image_finsih',
        'status',
        'alasan',
        'catatan'
    ];

    public function assignCleaner()
    {
        return $this->belongsToMany(User::class, 'cleaner_tasks', 'task_id', 'cleaner_id');
    }

    public function cleaner()
    {
        return $this->belongsTo(User::class, 'cleaner_id', 'id');
    }

    public function assign()
    {
        return $this->belongsTo(Assign::class, 'assign_id', 'id');
    }
}
