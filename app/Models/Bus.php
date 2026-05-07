<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'bus';
    protected $primaryKey = 'bus_id';
    public $timestamps = false;

    protected $guarded = [];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'bus_id');
    }
}
