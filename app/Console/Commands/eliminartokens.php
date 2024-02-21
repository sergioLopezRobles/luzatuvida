<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class eliminartokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comando:eliminartokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            DB::delete("DELETE FROM tokenlolatv");
        }catch(\Exception $e){
            \Log::info("Error: ".$e);
        }
        \Log::info("Los tokens se eliminaron correctamente.");
    }
}
