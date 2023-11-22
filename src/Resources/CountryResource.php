<?php

namespace Pardalsalcap\LinterLocations\Resources;

use Pardalsalcap\LinterLocations\Models\Continent;
use Pardalsalcap\LinterLocations\Models\Country;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pardalsalcap\LinterLocations\Resources\CountryResource\Pages\CreateCountry;
use Pardalsalcap\LinterLocations\Resources\CountryResource\Pages\EditCountry;
use Pardalsalcap\LinterLocations\Resources\CountryResource\Pages\ListCountries;
use Pardalsalcap\LinterLocations\Repositories\ContinentRepository;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

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
                                    TextInput::make('iso')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->minLength(2)
                                        ->maxLength(2)
                                        ->label(__('linter-locations::countries.iso_field')),
                                    TextInput::make('name')->required()
                                        ->label(__('linter-locations::countries.name_field')),
                                    Select::make('continent_id')
                                        ->relationship('continent', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(fn (Continent $continent) => $continent->translate(app()->getLocale()))
                                        ->label(__('linter-locations::countries.continent_id_field')),

                                ]),
                            ]),
                        Group::make()
                            ->schema([
                                Section::make()->schema(ContinentRepository::input_translatable('countries')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('iso')
                    ->label(__('linter-locations::countries.iso_column'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('linter-locations::countries.name_column'))
                    ->sortable()
                    ->formatStateUsing(function (Country $country){
                        return $country->translate(app()->getLocale());
                    })
                    ->searchable(),
                TextColumn::make('continent.name')
                    ->label(__('linter-locations::countries.continent_id_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (Country $country){
                        return $country->continent?->translate(app()->getLocale());
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
                    //
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
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::countries.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::countries.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::countries.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::countries.navigation_group');
    }
}
