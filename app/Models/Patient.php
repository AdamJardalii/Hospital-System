<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\BloodType;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'emergency_contact',
        'emergency_phone',
        'address',
        'medical_history',
        'allergies',
        'current_medications',
        'blood_type',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'allergies' => 'array',
            'current_medications' => 'array',
            'medical_history' => 'array',
            'blood_type' => BloodType::class,        
        ];
    }

    public function insuranceCard(): HasOne
    {
        return $this->hasOne(InsuranceCard::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
}