<?php

namespace Pardalsalcap\LinterLocations\Models;

use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $address_id
 * @property int $addressable_id
 * @property string $addressable_type
 * @property Address $address
 */
class Addressable extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addressables';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['address_id', 'addressable_id', 'addressable_type'];

    public function address(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Address');
    }
}
