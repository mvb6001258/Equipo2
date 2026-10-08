<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'product_name',
        'quantity',
        'harvest_date',
        'qr_code_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Batch $batch) {
            if (empty($batch->qr_code_token)) {
                $batch->qr_code_token = 'BATCH-' . strtoupper(Str::random(10));
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(TraceabilityEvent::class)->orderBy('id', 'asc');
    }
}
