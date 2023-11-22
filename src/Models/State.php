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
 * @property int $community_id
 * @property float $lat
 * @property float $lon
 * @property string $iso
 * @property string $po
 * @property string $name
 * @property mixed $translations
 * @property string $created_at
 * @property string $updated_at
 * @property Community $community
 * @property Country $country
 * @property City[] $cities
 *
 * @mixin Builder
 */
class State extends Model
{
    use HasTranslations, LocationsTrait;

    protected $keyType = 'integer';

    protected $fillable = ['country_id', 'community_id', 'lat', 'lon', 'iso', 'po', 'name', 'translations', 'created_at', 'updated_at'];

    protected $casts = [
        'translations' => 'array',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
