<?php

namespace Pardalsalcap\LinterLocations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Pardalsalcap\LinterLocations\Traits\HasTranslations;
use Pardalsalcap\LinterLocations\Traits\LocationsTrait;
use App\Models\User;

/**
 * @property int $id
 * @property int $city_id
 * @property float $lat
 * @property float $lon
 * @property string $po
 * @property string $address
 * @property string $number
 * @property string $stair
 * @property string $floor
 * @property string $door
 * @property string $created_at
 * @property string $updated_at
 * @property City $city
 * @property User[] $users
 *
 * @mixin Builder
 */
class Address extends Model
{
    use HasTranslations, LocationsTrait;

    protected $keyType = 'integer';

    protected $fillable = ['city_id', 'lat', 'lon', 'po', 'address', 'number', 'stair', 'floor', 'door', 'created_at', 'updated_at'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo()->withPivot(['address_type']);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', 'addressables', 'address_id', 'addressable_id', 'id', 'id')->withPivot(['address_type']);
    }

}
