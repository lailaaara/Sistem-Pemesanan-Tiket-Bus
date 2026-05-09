<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class, 'rute_id');
    }
}
