<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use SoftDeletes;

    protected $table = 'bus';
    protected $primaryKey = 'bus_id';
    public $timestamps = false;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'bus_id');
    }
}
