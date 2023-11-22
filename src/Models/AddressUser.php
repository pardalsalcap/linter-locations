<?php

namespace Pardalsalcap\LinterLocations\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $address_id
 * @property int $user_id
 * @property string $created_at
 * @property string $address_type
 * @property string $updated_at
 * @property Address $address
 * @property User $user
 *
 * @mixin Builder
 */
class AddressUser extends Model
{
    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = ['address_id', 'user_id', 'address_type', 'created_at', 'updated_at'];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
