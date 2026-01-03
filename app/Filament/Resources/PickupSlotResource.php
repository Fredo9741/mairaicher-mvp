<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PickupSlotResource\Pages;
use App\Models\PickupSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PickupSlotResource extends Resource
{
    protected static ?string $model = PickupSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Créneaux de retrait';

    protected static ?string $modelLabel = 'Créneau';

    protected static ?string $pluralModelLabel = 'Créneaux';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du créneau')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du créneau')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Matin 9h-12h')
                            ->helperText('Nom affiché au client'),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('Heure de début')
                            ->required()
                            ->seconds(false),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Heure de fin')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Créneau actif')
                            ->default(true)
                            ->helperText('Désactiver pour ne plus proposer ce créneau aux clients'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Début')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fin')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Commandes')
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Actif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_time', 'asc');
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
            'index' => Pages\ListPickupSlots::route('/'),
            'create' => Pages\CreatePickupSlot::route('/create'),
            'edit' => Pages\EditPickupSlot::route('/{record}/edit'),
        ];
    }
}
