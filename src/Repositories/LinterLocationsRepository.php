<?php

namespace Pardalsalcap\LinterLocations\Repositories;

use Filament\Forms\Components\TextInput;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;

class LinterLocationsRepository
{
    public static function input_translatable($module): array
    {
        $result = [];
        foreach (config('linter-locations.available_locales') as $iso => $language) {
            $result[] = TextInput::make("translations.{$iso}")->required()
                ->label(__("linter-locations::{$module}.translations_field", ['l' => $language]));
        }

        return $result;
    }

    public static function countriesAll()
    {
        return Country::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    public static function countryStates($country_id)
    {
        return State::where('country_id', $country_id)->orderBy('name', 'ASC')->pluck('name', 'id');
    }
}
