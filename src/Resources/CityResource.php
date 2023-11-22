<?php

namespace Pardalsalcap\LinterLocations\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pardalsalcap\LinterLocations\Models\City;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;
use Pardalsalcap\LinterLocations\Repositories\LinterLocationsRepository;
use Pardalsalcap\LinterLocations\Resources\CityResource\Pages\CreateCity;
use Pardalsalcap\LinterLocations\Resources\CityResource\Pages\EditCity;
use Pardalsalcap\LinterLocations\Resources\CityResource\Pages\ListCities;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make()->schema([
                                    TextInput::make('name')->required()
                                        ->label(__('linter-locations::cities.name_field')),
                                    Select::make('country_id')
                                        ->label(__('linter-locations::cities.country_id_field'))
                                        ->relationship('state.country')
                                        ->getOptionLabelFromRecordUsing(fn (Country $country) => $country->translate(app()->getLocale()))
                                        ->preload()
                                        ->searchable()
                                        ->reactive()
                                        ->dehydrated(false)
                                        ->live(onBlur: true)->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                                            $country = Country::find($state);
                                            if ($country) {
                                                $state_id = (int) $get('state_id');

                                                if ($state_id && $state_check = State::find($state_id)) {
                                                    if ($state_check->country_id !== $country->id) {
                                                        $set('state_id', null);
                                                    }
                                                }
                                            }
                                        })
                                        ->required(),
                                    Select::make('state_id')
                                        ->relationship('state', 'name')
                                        ->label(__('linter-locations::cities.state_id_field'))
                                        ->searchable()
                                        ->preload()
                                        ->options(function (callable $get, callable $set) {
                                            $country = Country::find($get('country_id'));
                                            if ($country) {
                                                return $country->states->pluck('name', 'id');
                                            }

                                            return State::all()->pluck('name', 'id');
                                        })
                                        ->required(),
                                ]),
                            ]),
                        Group::make()
                            ->schema([
                                Section::make()->schema(LinterLocationsRepository::input_translatable('cities')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('linter-locations::cities.name_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (City $record) {
                        return $record->translate(app()->getLocale());
                    }),
                TextColumn::make('state.name')
                    ->label(__('linter-locations::cities.state_id_column'))
                    ->sortable()
                    ->formatStateUsing(function (City $record) {
                        return $record->state?->translate(app()->getLocale());
                    }),
                TextColumn::make('state.country.name')
                    ->label(__('linter-locations::cities.country_id_column'))
                    ->sortable()
                    ->formatStateUsing(function (City $record) {
                        return $record->state?->country?->translate(app()->getLocale());
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
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::cities.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::cities.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::cities.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::cities.navigation_group');
    }
}
