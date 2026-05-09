<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSequences extends Command
{
    protected $signature = 'db:fix-sequences';
    protected $description = 'Fix auto-increment sequences in PostgreSQL';

    public function handle()
    {
        try {
            // Fix bus table sequence
            DB::statement("SELECT SETVAL((SELECT PG_GET_SERIAL_SEQUENCE('bus', 'bus_id')), (SELECT MAX(bus_id) FROM bus) + 1)");
            $this->info('✓ Bus sequence fixed');

            // Fix jadwal table sequence  
            DB::statement("SELECT SETVAL((SELECT PG_GET_SERIAL_SEQUENCE('jadwal', 'id_jadwal')), (SELECT MAX(id_jadwal) FROM jadwal) + 1)");
            $this->info('✓ Jadwal sequence fixed');

            // Fix rute table sequence
            DB::statement("SELECT SETVAL((SELECT PG_GET_SERIAL_SEQUENCE('rute', 'rute_id')), (SELECT MAX(rute_id) FROM rute) + 1)");
            $this->info('✓ Rute sequence fixed');

            $this->info('\n✅ All sequences have been fixed!');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
