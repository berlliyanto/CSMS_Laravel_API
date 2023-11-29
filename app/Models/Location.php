<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public function areas()
    {
        return $this->hasMany(Area::class, 'location_id', 'id');
    }

}
