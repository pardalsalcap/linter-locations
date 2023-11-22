<?php

namespace Pardalsalcap\LinterLocations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pardalsalcap\LinterLocations\Traits\HasTranslations;

/**
 * @property int $id
 * @property float $lat
 * @property float $lon
 * @property string $iso
 * @property string $name
 * @property mixed $translations
 * @property string $created_at
 * @property string $updated_at
 * @property Country[] $countries
 *
 * @mixin Builder
 */
class Continent extends Model
{
    use HasTranslations;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    protected $fillable = ['lat', 'lon', 'iso', 'name', 'translations', 'created_at', 'updated_at'];

    protected $casts = [
        'translations' => 'array',
    ];

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
