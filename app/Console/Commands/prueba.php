<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class prueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:prueba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("COMANDO DE PRUEBA EJECUTADO");
    }
}
