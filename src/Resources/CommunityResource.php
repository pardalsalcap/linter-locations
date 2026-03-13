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
use Pardalsalcap\LinterLocations\Repositories\LinterLocationsRepository;
use Pardalsalcap\LinterLocations\Resources\CommunityResource\Pages\CreateCommunity;
use Pardalsalcap\LinterLocations\Resources\CommunityResource\Pages\EditCommunity;
use Pardalsalcap\LinterLocations\Resources\CommunityResource\Pages\ListCommunities;

class CommunityResource extends Resource
{
    protected static ?string $model = Community::class;

    protected static ?int $navigationSort = 3;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-map';

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
                                        ->label(__('linter-locations::communities.iso_field')),
                                    TextInput::make('name')->required()
                                        ->label(__('linter-locations::communities.name_field')),
                                    Select::make('country_id')
                                        ->relationship('country', 'name')
                                        ->label(__('linter-locations::communities.country_id_field'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->getOptionLabelFromRecordUsing(fn (Country $country) => $country->translate(app()->getLocale())),
                                ]),
                            ]),
                        Group::make()
                            ->schema([
                                Section::make()->schema(LinterLocationsRepository::input_translatable('communities')),
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
                    ->label(__('linter-locations::communities.iso_column'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('linter-locations::communities.name_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (Community $community) {
                        return $community->translate(app()->getLocale());
                    }),
                TextColumn::make('country.name')
                    ->label(__('linter-locations::communities.country_id_column'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (Community $community) {
                        return $community->country?->translate(app()->getLocale());
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
            'index' => ListCommunities::route('/'),
            'create' => CreateCommunity::route('/create'),
            'edit' => EditCommunity::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('linter-locations::communities.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('linter-locations::communities.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linter-locations::communities.model_label_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('linter-locations::communities.navigation_group');
    }
}
