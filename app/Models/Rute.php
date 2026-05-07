<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    protected $table = 'rute';
    protected $primaryKey = 'rute_id';
    public $timestamps = false;

    protected $guarded = [];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'rute_id');
    }
}
