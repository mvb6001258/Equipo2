<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraceabilityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'stage',
        'description',
        'location',
        'actor',
        'previous_hash',
        'current_hash',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /**
     * Boot model and hook into creating event for cryptographic SHA-256 block linking.
     */
    protected static function booted(): void
    {
        static::creating(function (TraceabilityEvent $event) {
            // 1. Fetch the last event recorded for this batch
            $lastEvent = static::where('batch_id', $event->batch_id)
                ->orderBy('id', 'desc')
                ->first();

            // 2. Assign previous_hash (genesis hash if first block)
            if ($lastEvent) {
                $event->previous_hash = $lastEvent->current_hash;
            } else {
                // Genesis block hash default (64 zeroes)
                $event->previous_hash = str_repeat('0', 64);
            }

            // 3. Ensure timestamp is set
            if (empty($event->recorded_at)) {
                $event->recorded_at = now();
            }

            // 4. Calculate current SHA-256 hash
            $event->current_hash = static::generateHash(
                $event->batch_id,
                $event->stage,
                $event->description,
                $event->location,
                $event->actor,
                $event->previous_hash,
                $event->recorded_at
            );
        });
    }

    /**
     * Compute SHA-256 cryptographic hash for event payload.
     */
    public static function generateHash(
        int $batchId,
        string $stage,
        string $description,
        string $location,
        string $actor,
        string $previousHash,
        $recordedAt
    ): string {
        $timestampString = is_string($recordedAt) ? $recordedAt : $recordedAt->toIso8601String();
        
        $payload = implode('|', [
            $batchId,
            trim($stage),
            trim($description),
            trim($location),
            trim($actor),
            trim($previousHash),
            $timestampString
        ]);

        return hash('sha256', $payload);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
