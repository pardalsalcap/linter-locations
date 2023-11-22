<?php

namespace Pardalsalcap\LinterLocations\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pardalsalcap\LinterLocations\Models\Continent;
use Pardalsalcap\LinterLocations\Repositories\ContinentRepository;
use Pardalsalcap\LinterLocations\Resources\ContinentResource\Pages\CreateContinent;
use Pardalsalcap\LinterLocations\Resources\ContinentResource\Pages\EditContinent;
use Pardalsalcap\LinterLocations\Resources\ContinentResource\Pages\ListContinents;

class ContinentResource extends Resource
{
    protected static ?string $model = Continent::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

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
                                        ->label(__('linter-locations::continents.iso_field'))
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->minLength(2)
                                        ->maxLength(2),
                                    TextInput::make('name')
                                        ->label(__('linter-locations::continents.name_field'))
                                        ->required(),
                                    TextInput::make('lat')
                                        ->label(__('linter-locations::continents.lat_field'))
                                        ->numeric()
                                        ->rules(['decimal:1,7', 'min:-90', 'max:90']),
                                    TextInput::make('lon')
                                        ->label(__('linter-locations::continents.lon_field'))
                                        ->numeric()
                                        ->rules(['decimal:1,7', 'min:-180', 'max:180']),
                                ]),
                            ]),
                        Group::make()
                            ->schema([
                                Section::make()->schema(ContinentRepository::input_translatable('continents')),
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
                    ->label(__('linter-locations::continents.iso_column')),
                TextColumn::make('name')
                    ->label(__('linter-locations::continents.name_column'))
                    ->formatStateUsing(function (Continent $continent) {
                        return $continent->translate(app()->getLocale());
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
            'index' => ListContinents::route('/'),
            'create' => CreateContinent::route('/create'),
            'edit' => EditContinent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::continents.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::continents.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::continents.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::continents.navigation_group');
    }
}
