<?php

namespace Pardalsalcap\LinterLocations\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Pardalsalcap\LinterLocations\Models\Address;
use Pardalsalcap\LinterLocations\Models\City;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;
use Pardalsalcap\LinterLocations\Repositories\LinterLocationsRepository;
use Pardalsalcap\LinterLocations\Resources\AddressResource\Pages\CreateAddress;
use Pardalsalcap\LinterLocations\Resources\AddressResource\Pages\EditAddress;
use Pardalsalcap\LinterLocations\Resources\AddressResource\Pages\ListAddresses;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('address')
                    ->label(__('linter-locations::addresses.address_field'))
                    ->required()
                    ->maxLength(255),
                Group::make()->columns(4)->schema([
                    TextInput::make('number')
                        ->label(__('linter-locations::addresses.number_field'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('stair')
                        ->label(__('linter-locations::addresses.stair_field'))
                        ->maxLength(255),
                    TextInput::make('floor')
                        ->label(__('linter-locations::addresses.floor_field'))
                        ->maxLength(255),
                    TextInput::make('door')
                        ->label(__('linter-locations::addresses.door_field'))
                        ->maxLength(255),
                ]),
                Select::make('country_id')
                    ->label(__('linter-locations::addresses.country_id_field'))
                    ->options(LinterLocationsRepository::countriesAll())
                    //->relationship('city.state.country')
                    //->getOptionLabelFromRecordUsing(fn(Country $country) => $country->translate(app()->getLocale()))
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->dehydrated(false)
                    ->live(onBlur: true)->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                        $country = Country::find($state);

                        if ($country) {
                            $state_id = (int) $get('state_id');
                            $city_id = (int) $get('city_id');

                            if ($state_id && $stateModel = State::find($state_id)) {
                                if ($stateModel->country_id !== $country->id) {
                                    $set('state_id', null);
                                    $set('city_id', null);
                                } elseif ($city_id && $city = City::find($city_id)) {
                                    if ($city->state_id !== $stateModel->id) {
                                        $set('city_id', null);
                                    }
                                }
                            }
                        }
                    })
                    ->required(),
                Select::make('state_id')
                    ->label(__('linter-locations::addresses.state_id_field'))

                    /*->disabled(function (Get $get, Set $set) {
                        return empty($get('country_id'));
                    })*/
                    ->options(function (Get $get, Set $set) {
                        $country_id = (int) $get('country_id');
                        if ($country_id) {
                            return LinterLocationsRepository::countryStates($country_id);
                        }

                        return ['' => __('linter-locations::addresses.state_id_placeholder')];
                    })
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->dehydrated(false)
                    ->live(onBlur: true)->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                        $state = State::find($state);

                        if ($state) {
                            $city_id = (int) $get('city_id');

                            if ($city_id && $city = City::find($city_id)) {
                                if ($city->state_id !== $state->id) {
                                    $set('city_id', null);
                                }
                            }
                        }
                    })
                    ->required(),
                Select::make('city_id')
                    ->label(__('linter-locations::addresses.city_id_field'))
                    ->options(function (callable $get, callable $set) {
                        $state = State::find($get('state_id'));
                        if ($state) {
                            return $state->cities->pluck('name', 'id');
                        }

                        //return City::all()->pluck('name', 'id');
                        return ['' => __('linter-locations::addresses.city_id_placeholder')];
                    })
                    ->searchable(),

                TextInput::make('lat')
                    ->label(__('linter-locations::addresses.lat_field'))
                    ->maxLength(255),
                TextInput::make('lon')
                    ->label(__('linter-locations::addresses.lon_field'))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('address')
                    ->formatStateUsing(function (Model $model) {
                        return $model->address.' '.$model->number.', '.$model->stair.' '.$model->floor.' '.$model->door;
                    })
                    ->label(__('linter-locations::addresses.address_column')),
                TextColumn::make('city.name')
                    ->label(__('linter-locations::addresses.city_id_column'))
                    ->formatStateUsing(function (Address $record) {
                        return $record->city?->translate(app()->getLocale());
                    }),
                TextColumn::make('city.state.name')
                    ->label(__('linter-locations::addresses.state_id_column'))
                    ->formatStateUsing(function (Address $record) {
                        return $record->city?->state?->translate(app()->getLocale());
                    }),
                TextColumn::make('city.state.country.name')
                    ->label(__('linter-locations::addresses.country_id_column'))
                    ->formatStateUsing(function (Address $record) {
                        return $record->city?->state?->country?->translate(app()->getLocale());
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAddresses::route('/'),
            'create' => CreateAddress::route('/create'),
            'edit' => EditAddress::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::addresses.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::addresses.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::addresses.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::addresses.navigation_group');
    }
}
