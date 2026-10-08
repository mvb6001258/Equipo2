<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Farm;
use App\Models\TraceabilityEvent;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    /**
     * Display a listing of all registered agricultural batches.
     */
    public function index()
    {
        $batches = Batch::with(['farm', 'events'])->orderBy('id', 'desc')->get();
        $farms = Farm::all();

        return view('traceability.index', compact('batches', 'farms'));
    }

    /**
     * Display the blockchain timeline for a specific batch identified by QR Token or ID.
     */
    public function show($qr_code_token)
    {
        $batch = Batch::with(['farm', 'events'])
            ->where('qr_code_token', $qr_code_token)
            ->orWhere('id', $qr_code_token)
            ->firstOrFail();

        // Audit chain integrity
        $chainStatus = $this->auditChainIntegrity($batch);

        return view('traceability.timeline', compact('batch', 'chainStatus'));
    }

    /**
     * Store a new farm.
     */
    public function storeFarm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
        ]);

        Farm::create($validated);

        return redirect()->route('traceability.index')->with('success', 'Finca agrícola registrada exitosamente.');
    }

    /**
     * Store a new agricultural batch.
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'harvest_date' => 'required|date',
            'qr_code_token' => 'nullable|string|max:255|unique:batches,qr_code_token',
        ]);

        $batch = Batch::create($validated);

        return redirect()->route('traceability.show', $batch->qr_code_token)
            ->with('success', 'Lote agrícola creado exitosamente. Puede comenzar a agregar eventos en la cadena.');
    }

    /**
     * Store a new traceability event block (calculates SHA-256 hash & previous hash automatically).
     */
    public function storeEvent(Request $request, $batchId)
    {
        $batch = Batch::findOrFail($batchId);

        $validated = $request->validate([
            'stage' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'actor' => 'required|string|max:255',
            'recorded_at' => 'nullable|date',
        ]);

        if (empty($validated['recorded_at'])) {
            $validated['recorded_at'] = now();
        }

        $validated['batch_id'] = $batch->id;

        TraceabilityEvent::create($validated);

        return redirect()->route('traceability.show', $batch->qr_code_token)
            ->with('success', '¡Nuevo bloque registrado criptográficamente en la cadena de trazabilidad!');
    }

    /**
     * Check cryptographic integrity of the blockchain for a batch.
     */
    private function auditChainIntegrity(Batch $batch): array
    {
        $events = $batch->events;
        $isValid = true;
        $invalidBlockId = null;
        $expectedPreviousHash = str_repeat('0', 64);

        foreach ($events as $index => $event) {
            // Check previous hash link matches
            if ($event->previous_hash !== $expectedPreviousHash) {
                $isValid = false;
                $invalidBlockId = $event->id;
                break;
            }

            // Recalculate hash to detect tampering
            $recalculatedHash = TraceabilityEvent::generateHash(
                $event->batch_id,
                $event->stage,
                $event->description,
                $event->location,
                $event->actor,
                $event->previous_hash,
                $event->recorded_at
            );

            if ($event->current_hash !== $recalculatedHash) {
                $isValid = false;
                $invalidBlockId = $event->id;
                break;
            }

            // Chain next expected hash
            $expectedPreviousHash = $event->current_hash;
        }

        return [
            'is_valid' => $isValid,
            'total_blocks' => count($events),
            'tampered_block_id' => $invalidBlockId,
        ];
    }
}
