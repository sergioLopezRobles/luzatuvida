<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class eliminardocumentoscontrato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminardocumentos:contrato';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando elimina los documentos de un contrato despues de que ya esta liquidado.';

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
        $ahora = Carbon::now();
        
        $contratos = DB::select("SELECT * FROM contratos WHERE estatus_estadocontrato in (5,6,8) AND DATEDIFF('$ahora',updated_at)  > 365");
        foreach ($contratos as $contrato) {
            try {
                Storage::disk('disco')->delete($contrato->fotoine);
                Storage::disk('disco')->delete($contrato->fotoineatras);
                Storage::disk('disco')->delete($contrato->fotocasa);
                Storage::disk('disco')->delete($contrato->comprobantedomicilio);
                Storage::disk('disco')->delete($contrato->tarjeta);
                Storage::disk('disco')->delete($contrato->tarjetapensionatras);
            }catch(\Exception $e){
                \Log::info("Error: ".$e);
                continue;
            }
        }
       
        \Log::info("Los documentos de los contratos se eliminaron correctamente.");
    }
}
