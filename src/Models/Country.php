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
 * @property int $continent_id
 * @property float $lat
 * @property float $lon
 * @property string $iso
 * @property string $iso3
 * @property string $name
 * @property mixed $translations
 * @property string $created_at
 * @property string $updated_at
 * @property Continent $continent
 * @property Community[] $communities
 * @property State[] $states
 *
 * @mixin Builder
 */
class Country extends Model
{
    use HasTranslations, LocationsTrait;

    protected $keyType = 'integer';

    protected $fillable = ['continent_id', 'lat', 'lon', 'iso', 'iso3', 'name', 'translations', 'created_at', 'updated_at'];

    protected $casts = [
        'translations' => 'array',
    ];

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class);
    }

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
