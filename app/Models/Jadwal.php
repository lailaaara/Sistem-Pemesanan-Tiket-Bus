<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $guarded = [];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class, 'rute_id');
    }
}
