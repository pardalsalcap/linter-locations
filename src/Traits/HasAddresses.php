<?php

namespace Pardalsalcap\LinterLocations\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Pardalsalcap\LinterLocations\Models\Address;
use Pardalsalcap\LinterLocations\Models\AddressModel;

trait HasAddresses
{
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable')->withPivot(['address_type']);
    }

    public function attachAddress(Address $address)
    {
        $addressable = AddressModel::where('addressable_id', $this->id)
            ->where('addressable_type', self::class)
            ->where('address_id', $address->id)
            ->firstOrNew();
        $addressable->address_id = $address->id;
        $addressable->addressable_id = $this->id;
        $addressable->addressable_type = self::class;

        return $addressable->save();
    }

    public function detachAddress(Address $address)
    {
        return AddressModel::where('address_id', $address->id)
            ->where('addressable_id', $this->id)
            ->where('addressable_type', self::class)
            ->delete();
    }

    public function syncAddresses(...$addresses)
    {
        $addresses = collect($addresses);
        $addresses_ids = $addresses->pluck('id')->all();

        AddressModel::where('addressable_id', $this->id)
            ->where('addressable_type', self::class)
            ->whereNotIn('address_id', $addresses_ids)
            ->delete();
        foreach ($addresses as $address) {
            $this->attachAddress($address);
        }
    }
}
