<?php

namespace Pardalsalcap\LinterLocations\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Pardalsalcap\LinterLocations\Models\Continent;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        if (! config('linter-locations.use_scoped_search')) {
            return parent::applySearchToTableQuery($query);
        }
        if (filled($searchQuery = $this->getTableSearch())) {
            $matching_continents = Continent::where('name', 'LIKE', '%'.strtolower($searchQuery).'%')
                ->orWhereRaw("LOWER(translations) LIKE '%".strtolower($searchQuery)."%' ")
                ->pluck('id');

            return $query->whereRaw("LOWER(translations) LIKE '%".strtolower($searchQuery)."%' ")
                ->orWhere('name', 'LIKE', '%'.strtolower($searchQuery).'%')
                ->when(count($matching_continents) > 0, function ($query) use ($matching_continents) {
                    $query->orWhereIn('continent_id', $matching_continents);
                });
        }

        return $query;
    }
}
