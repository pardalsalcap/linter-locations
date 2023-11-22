<?php

namespace Pardalsalcap\LinterLocations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pardalsalcap\LinterLocations\Traits\HasTranslations;
use Pardalsalcap\LinterLocations\Traits\LocationsTrait;

/**
 * @property int $id
 * @property int $country_id
 * @property float $lat
 * @property float $lon
 * @property string $iso
 * @property string $name
 * @property mixed $translations
 * @property string $created_at
 * @property string $updated_at
 * @property Country $country
 * @property State[] $states
 *
 * @mixin Builder
 */
class Community extends Model
{
    use HasTranslations, LocationsTrait;

    protected $keyType = 'integer';

    protected $fillable = ['country_id', 'lat', 'lon', 'iso', 'name', 'translations', 'created_at', 'updated_at'];

    protected $casts = [
        'translations' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'comunity_id');
    }
}
