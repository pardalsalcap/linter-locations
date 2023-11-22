<?php

namespace Pardalsalcap\LinterLocations\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCity extends EditRecord
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $arr = $this->record->toArray();
        if (!empty($arr['state_id']))
        {
            $arr['country_id']=$this->record->state->country_id;
        }
        $this->form->fill($arr);
    }
}
