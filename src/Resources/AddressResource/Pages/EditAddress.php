<?php

namespace Pardalsalcap\LinterLocations\Resources\AddressResource\Pages;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAddress extends EditRecord
{
    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $arr = $this->record->toArray();
        if (! empty($arr['city_id'])) {
            $arr['state_id'] = $this->record->city->state_id;
            $arr['country_id'] = $this->record->city->state->country_id;
        }
        $this->form->fill($arr);
    }
}
