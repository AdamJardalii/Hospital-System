<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'policy_number',
        'provider_name',
        'group_number',
        'effective_date',
        'expiry_date',
        'card_image_path',
        'ocr_raw_data',
        'confidence_score',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'expiry_date' => 'date',
            'ocr_raw_data' => 'array',
            'confidence_score' => 'decimal:2',
            'is_verified' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}