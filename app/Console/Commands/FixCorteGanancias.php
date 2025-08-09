<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CorteCaja;
use App\Models\Venta;

class FixCorteGanancias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corte:fix-ganancias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix total_ganancias in CorteCaja records by summing individual sale gains';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix CorteCaja total_ganancias...');

        $cortes = CorteCaja::all();
        $fixedCount = 0;

        foreach ($cortes as $corte) {
            // Get all sales for this corte
            $ventas = Venta::whereDate('fecha', $corte->fecha_inicio)->get();
            
            // Calculate correct total_ganancias
            $totalGanancias = $ventas->sum('ganancia');
            
            // Update if different
            if ($corte->total_ganancias != $totalGanancias) {
                $oldValue = $corte->total_ganancias;
                $corte->total_ganancias = $totalGanancias;
                $corte->save();
                
                $this->info("Fixed corte {$corte->id} ({$corte->fecha_inicio}): {$oldValue} -> {$totalGanancias}");
                $fixedCount++;
            }
        }

        $this->info("Completed! Fixed {$fixedCount} CorteCaja records.");
        
        return 0;
    }
} 