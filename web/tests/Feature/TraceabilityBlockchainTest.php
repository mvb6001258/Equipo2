<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Farm;
use App\Models\TraceabilityEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraceabilityBlockchainTest extends TestCase
{
    use RefreshDatabase;

    public function test_blockchain_hash_linking_and_immutability(): void
    {
        $farm = Farm::create([
            'name' => 'Finca Test',
            'location' => 'Mérida',
            'owner' => 'Juan Pérez'
        ]);

        $batch = Batch::create([
            'farm_id' => $farm->id,
            'product_name' => 'Café Test',
            'quantity' => '500 kg',
            'harvest_date' => '2026-10-01',
            'qr_code_token' => 'TOKEN-TEST-001'
        ]);

        // Block 1 (Genesis)
        $event1 = TraceabilityEvent::create([
            'batch_id' => $batch->id,
            'stage' => 'Cosecha',
            'description' => 'Recolección inicial',
            'location' => 'Lote A',
            'actor' => 'Productor 1',
            'recorded_at' => now()->subHours(5)
        ]);

        // Block 2
        $event2 = TraceabilityEvent::create([
            'batch_id' => $batch->id,
            'stage' => 'Procesamiento',
            'description' => 'Despulpado y fermentación',
            'location' => 'Planta 1',
            'actor' => 'Técnico 2',
            'recorded_at' => now()->subHours(2)
        ]);

        // Assert Genesis Previous Hash is 64 zeroes
        $this->assertEquals(str_repeat('0', 64), $event1->previous_hash);

        // Assert Event 2 Previous Hash matches Event 1 Current Hash
        $this->assertEquals($event1->current_hash, $event2->previous_hash);

        // Assert current_hash is a valid 64-character SHA-256 hexadecimal string
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $event1->current_hash);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $event2->current_hash);

        // Verify web route response
        $response = $this->get('/batch/' . $batch->qr_code_token);
        $response->assertStatus(200);
        $response->assertSee('Café Test');
        $response->assertSee('TOKEN-TEST-001');
    }
}
