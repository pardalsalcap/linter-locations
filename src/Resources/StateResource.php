<?php

namespace Pardalsalcap\LinterLocations\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pardalsalcap\LinterLocations\Models\Community;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;
use Pardalsalcap\LinterLocations\Repositories\LinterLocationsRepository;
use Pardalsalcap\LinterLocations\Resources\StateResource\Pages\CreateState;
use Pardalsalcap\LinterLocations\Resources\StateResource\Pages\EditState;
use Pardalsalcap\LinterLocations\Resources\StateResource\Pages\ListStates;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                        ->label(__('linter-locations::states.iso_field')),
                                    TextInput::make('name')->required()
                                        ->label(__('linter-locations::states.name_field')),
                                    Select::make('country_id')
                                        ->relationship('country', 'name')
                                        ->label(__('linter-locations::states.country_id_field'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->getOptionLabelFromRecordUsing(fn (Country $country) => $country->translate(app()->getLocale())),
                                    Select::make('community_id')
                                        ->relationship('community', 'name')
                                        ->label(__('linter-locations::states.community_id_field'))
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(fn (Community $community) => $community->translate(app()->getLocale())),
                                ]),
                            ]),
                        Group::make()
                            ->schema([
                                Section::make()->schema(LinterLocationsRepository::input_translatable('states')),
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
                TextColumn::make('iso')
                    ->label(__('linter-locations::states.iso_column'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('linter-locations::states.name_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (State $record) {
                        return $record->translate(app()->getLocale());
                    }),
                TextColumn::make('country.name')
                    ->label(__('linter-locations::states.country_id_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (State $record) {
                        return $record->country?->translate(app()->getLocale());
                    }),
                TextColumn::make('community.name')
                    ->label(__('linter-locations::states.community_id_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (State $record) {
                        return $record->community?->translate(app()->getLocale());
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
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
            'index' => ListStates::route('/'),
            'create' => CreateState::route('/create'),
            'edit' => EditState::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::states.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::states.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::states.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::states.navigation_group');
    }
}
