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
 * @property int $state_id
 * @property float $lat
 * @property float $lon
 * @property string $po
 * @property string $name
 * @property mixed $translations
 * @property string $created_at
 * @property string $updated_at
 * @property State $state
 * @property Address[] $addresses
 *
 * @mixin Builder
 */
class City extends Model
{
    use HasTranslations, LocationsTrait;

    protected $keyType = 'integer';

    protected $fillable = ['state_id', 'lat', 'lon', 'po', 'name', 'translations', 'created_at', 'updated_at'];

    protected $casts = [
        'translations' => 'array',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
