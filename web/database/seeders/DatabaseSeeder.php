<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Batch;
use App\Models\TraceabilityEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic agricultural traceability demo data.
     */
    public function run(): void
    {
        // 1. Create Demo Farms
        $farm1 = Farm::create([
            'name' => 'Hacienda Valle Verde',
            'location' => 'Sector El Valle, Mérida - Venezuela',
            'owner' => 'Ing. Carlos Mendoza',
        ]);

        $farm2 = Farm::create([
            'name' => 'Finca Agroecológica El Roble',
            'location' => 'Carora, Estado Lara - Venezuela',
            'owner' => 'Dra. Elena Ramos',
        ]);

        // 2. Create Demo Batches
        $batch1 = Batch::create([
            'farm_id' => $farm1->id,
            'product_name' => 'Café Arábica Orgánico Gourmet (Saquitos 500g)',
            'quantity' => '1,500 kg',
            'harvest_date' => Carbon::now()->subDays(15)->toDateString(),
            'qr_code_token' => 'CAFE-2026-VALLE-001',
        ]);

        $batch2 = Batch::create([
            'farm_id' => $farm2->id,
            'product_name' => 'Cacao Criollo Fino de Aroma',
            'quantity' => '800 kg',
            'harvest_date' => Carbon::now()->subDays(30)->toDateString(),
            'qr_code_token' => 'CACAO-2026-ROBLE-002',
        ]);

        // 3. Create Sequential Traceability Events (Blockchain blocks for Batch 1)
        $t1 = Carbon::now()->subDays(15)->setHour(8);
        TraceabilityEvent::create([
            'batch_id' => $batch1->id,
            'stage' => 'Cosecha Seleccionada',
            'description' => 'Recolección manual de cerezas rojas de café en punto óptimo de maduración. Verificación microbiológica inicial realizada.',
            'location' => 'Hacienda Valle Verde - Lote A3',
            'actor' => 'Cuadrilla de Cosecha N° 4',
            'recorded_at' => $t1,
        ]);

        $t2 = Carbon::now()->subDays(12)->setHour(14);
        TraceabilityEvent::create([
            'batch_id' => $batch1->id,
            'stage' => 'Despulpado y Fermentación Controlada',
            'description' => 'Despulpado mecánico en seco y fermentación en tanques de acero por 36 horas a 18°C. PH final: 4.2.',
            'location' => 'Planta Beneficiadero Valle Verde',
            'actor' => 'Téc. Luis Paredes (Control de Calidad)',
            'recorded_at' => $t2,
        ]);

        $t3 = Carbon::now()->subDays(8)->setHour(10);
        TraceabilityEvent::create([
            'batch_id' => $batch1->id,
            'stage' => 'Secado Solar y Trillado',
            'description' => 'Secado uniforme en marquesinas solares hasta alcanzar 11% de humedad relativa. Trillado y eliminación de pasilla.',
            'location' => 'Centro de Beneficio Seco',
            'actor' => 'Ing. Sofía Torrealba',
            'recorded_at' => $t3,
        ]);

        $t4 = Carbon::now()->subDays(4)->setHour(16);
        TraceabilityEvent::create([
            'batch_id' => $batch1->id,
            'stage' => 'Empaque Ecotrazable y Sellado',
            'description' => 'Empacado hermético en bolsas GrainPro y sacos de yute ecotrazables etiquetados con token QR individual.',
            'location' => 'Almacén Central de Despacho',
            'actor' => 'Supervisión de Empaque y Logística',
            'recorded_at' => $t4,
        ]);

        $t5 = Carbon::now()->subDays(1)->setHour(9);
        TraceabilityEvent::create([
            'batch_id' => $batch1->id,
            'stage' => 'Transporte y Custodia de Cadena',
            'description' => 'Despacho en camión refrigerado termo-monitoreado (placa A92-X88) con destino al centro de distribución regional.',
            'location' => 'Ruta Nacional N° 7 - En Tránsito',
            'actor' => 'TransAgro Logística S.A.',
            'recorded_at' => $t5,
        ]);

        // 4. Create Traceability Events for Batch 2
        TraceabilityEvent::create([
            'batch_id' => $batch2->id,
            'stage' => 'Cosecha de Mazorcas Criollas',
            'description' => 'Corte manual de mazorcas maduras de cacao criollo certificado.',
            'location' => 'Finca El Roble - Tablas 1 y 2',
            'actor' => 'Roberto Colmenarez',
            'recorded_at' => Carbon::now()->subDays(30)->setHour(7),
        ]);

        TraceabilityEvent::create([
            'batch_id' => $batch2->id,
            'stage' => 'Fermentación en Cajas de Madera',
            'description' => 'Fermentación en cajas de madera de saqui-saqui durante 5 días con volteo diario.',
            'location' => 'Beneficiadero El Roble',
            'actor' => 'Dra. Elena Ramos',
            'recorded_at' => Carbon::now()->subDays(24)->setHour(11),
        ]);
    }
}
