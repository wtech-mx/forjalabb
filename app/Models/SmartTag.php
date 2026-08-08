<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'type',
    'is_active',
    'tag_code',
    'display_name',
    'owner_name',
    'owner_phone',
    'owner_email',
    'secondary_contact_name',
    'secondary_contact_phone',
    'secondary_contact_email',
    'blood_type',
    'is_blood_donor',
    'allergies',
    'medical_notes',
    'public_notes',
    'vehicle',
    'motorcycle_plate',
    'has_vehicle_insurance',
    'vehicle_insurance_policy',
    'vehicle_insurance_expires_at',
    'has_public_health_insurance',
    'public_health_provider',
    'public_health_number',
    'club_name',
    'pet_species',
    'pet_breed',
    'vet_name',
    'vet_phone',
    'vet_email',
    'activated_at',
    'expires_at',
])]
class SmartTag extends Model
{
    use HasFactory;

    public const TYPE_BIKER = 'biker';

    public const TYPE_DOG = 'dog';

    protected static function booted(): void
    {
        static::creating(function (SmartTag $tag): void {
            $tag->token ??= Str::random(12);
            $tag->activated_at ??= now();
        });

        static::created(function (SmartTag $tag): void {
            $tag->forceFill(['tag_code' => $tag->buildAutoCode()])->saveQuietly();
        });

        static::updating(function (SmartTag $tag): void {
            if ($tag->isDirty(['type', 'blood_type', 'is_blood_donor'])) {
                $tag->tag_code = $tag->buildAutoCode();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_blood_donor' => 'boolean',
            'has_vehicle_insurance' => 'boolean',
            'vehicle_insurance_expires_at' => 'date',
            'has_public_health_insurance' => 'boolean',
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function bloodTypes(): array
    {
        return ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];
    }

    /**
     * @return array<string, string>
     */
    public static function publicHealthProviders(): array
    {
        return [
            'imss' => 'IMSS',
            'issste' => 'ISSSTE',
            'pemex' => 'PEMEX',
            'sedena' => 'SEDENA (servicio militar)',
            'semar' => 'SEMAR (servicio naval)',
            'imss_bienestar' => 'IMSS-Bienestar',
            'estatal' => 'Servicio estatal de salud',
            'otro' => 'Otro',
        ];
    }

    public function getPublicHealthProviderLabelAttribute(): ?string
    {
        return self::publicHealthProviders()[$this->public_health_provider] ?? $this->public_health_provider;
    }

    public function buildAutoCode(): string
    {
        $product = match ($this->type) {
            self::TYPE_BIKER => 'BKR',
            self::TYPE_DOG => 'DOG',
            default => 'TAG',
        };

        $bloodType = $this->blood_type ?: 'XX';
        $blood = str_replace(['+', '-'], ['P', 'N'], strtoupper($bloodType));
        $donor = $this->type === self::TYPE_BIKER
            ? ($this->is_blood_donor ? 'D' : 'ND')
            : 'PET';

        return sprintf('LC-%s-%s-%s-%06d', $product, $blood, $donor, $this->id ?: 0);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_BIKER => 'Biker Tag',
            self::TYPE_DOG => 'Dog Tag',
            default => 'Smart Tag',
        };
    }

    public function getPublicUrlAttribute(): string
    {
        return route('tags.public', $this->token);
    }
}
