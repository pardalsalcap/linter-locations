<?php

namespace Pardalsalcap\LinterLocations\Traits;

use Pardalsalcap\LinterLocations\Models\Address;
use Pardalsalcap\LinterLocations\Models\City;
use Pardalsalcap\LinterLocations\Models\Community;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;

trait LocationsTrait
{
    public function alternativeContinent($alternative)
    {
        return Country::where('continent_id', $this->id)->update(['continent_id' => $alternative]);
    }

    public function alternativeCountry($alternative)
    {
        State::where('country_id', $this->id)->update(['country_id' => $alternative]);
        Community::where('country_id', $this->id)->update(['country_id' => $alternative]);
    }

    public function alternativeCommunity($alternative)
    {
        State::where('community_id', $this->id)->update(['community_id' => $alternative]);
    }

    public function alternativeState($alternative)
    {
        return City::where('state_id', $this->id)->update(['state_id' => $alternative]);
    }

    public function alternativeCity($alternative)
    {
        return Address::where('city_id', $this->id)->update(['city_id' => $alternative]);
    }

    public function adaptCoordinates($coordinate = null, $precision = 7): float
    {
        return round($coordinate ?? 0, $precision);
    }
}
