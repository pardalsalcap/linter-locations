<?php

namespace Pardalsalcap\LinterLocations\Resources\AddressResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Pardalsalcap\LinterLocations\Models\Address;
use Pardalsalcap\LinterLocations\Models\Addressable;
use Pardalsalcap\LinterLocations\Models\City;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;
use Pardalsalcap\LinterLocations\Repositories\LinterLocationsRepository;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $inverseRelationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('address_type')
                    ->label(__('linter::addresses.address_type_field'))
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->required(),
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

                            if ($state_id && $stateModel = \Pardalsalcap\LinterLocations\Models\State::find($state_id)) {
                                if ($stateModel->country_id !== $country->id) {
                                    $set('state_id', null);
                                    $set('city_id', null);
                                } elseif ($city_id && $city = \Pardalsalcap\LinterLocations\Models\City::find($city_id)) {
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                Tables\Columns\TextColumn::make('address_type')
                    ->label(__('linter::addresses.address_type_column')),
                Tables\Columns\TextColumn::make('address')
                    ->formatStateUsing(function (Model $model) {
                        return $model->address.' '.$model->number.', '.$model->stair.' '.$model->floor.' '.$model->door;
                    })
                    ->label(__('linter::addresses.address_column')),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('linter::addresses.city_id_column')),
                Tables\Columns\TextColumn::make('city.state.name')
                    ->label(__('linter::addresses.state_id_column')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->recordSelect(
                        fn () => Select::make('recordId')
                            ->options(function () {
                                return Address::whereNotIn('addresses.id',
                                    Addressable::where('addressable_type', $this->ownerRecord::class)
                                        ->where('addressable_id', $this->ownerRecord->id)
                                        ->pluck('address_id')
                                )
                                    ->join('cities', 'cities.id', '=', 'addresses.city_id')
                                    ->pluck(DB::raw('concat(address, " ", COALESCE(`number`,\'\'), ", ", COALESCE(`stair`,\'\'), " ", COALESCE(`floor`,\'\'), " ", COALESCE(`door`,\'\'), " ", COALESCE(addresses.`po`,\'\'), " ", cities.name) as name'), 'addresses.id');

                            })
                    )
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('address_type')
                            ->required()
                            ->label(__('linter::addresses.address_type_field')),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Address $address): array {
                        if (! empty($data['city_id'])) {
                            $data['state_id'] = $address->city->state_id;
                            $data['country_id'] = $address->city->state->country_id;
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('linter-locations::addresses.relation_address_title');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::addresses.relation_address');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::addresses.relation_address_plural');
    }
}
