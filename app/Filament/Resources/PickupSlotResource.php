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
                Forms\Components\Section::make('Informations du point de retrait')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du point de retrait')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Parking Covoiturage Saint-Leu Centre')
                            ->helperText('Nom affiché au client')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('address')
                            ->label('Adresse complète')
                            ->maxLength(255)
                            ->placeholder('Ex: Avenue du Général de Gaulle, 97436 Saint-Leu')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Point actif')
                            ->default(true)
                            ->helperText('Désactiver pour ne plus proposer ce point aux clients')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Localisation GPS')
                    ->description('Cliquez sur la carte pour définir la position exacte du point de retrait')
                    ->schema([
                        Forms\Components\View::make('filament.forms.components.map-picker')
                            ->label('Carte')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('lat')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('-21.17')
                            ->helperText('Latitude GPS (rempli automatiquement depuis la carte)'),

                        Forms\Components\TextInput::make('lng')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('55.29')
                            ->helperText('Longitude GPS (rempli automatiquement depuis la carte)'),
                    ])->columns(2),

                Forms\Components\Section::make('Horaires d\'ouverture')
                    ->description('Définissez les horaires où les clients peuvent venir retirer leurs commandes')
                    ->schema([
                        Forms\Components\Repeater::make('working_hours')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('day')
                                    ->label('Jour')
                                    ->required()
                                    ->options([
                                        'monday' => 'Lundi',
                                        'tuesday' => 'Mardi',
                                        'wednesday' => 'Mercredi',
                                        'thursday' => 'Jeudi',
                                        'friday' => 'Vendredi',
                                        'saturday' => 'Samedi',
                                        'sunday' => 'Dimanche',
                                    ])
                                    ->native(false)
                                    ->distinct(),

                                Forms\Components\TimePicker::make('open')
                                    ->label('Heure d\'ouverture')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat('H:i'),

                                Forms\Components\TimePicker::make('close')
                                    ->label('Heure de fermeture')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat('H:i')
                                    ->after('open'),

                                Forms\Components\Toggle::make('closed')
                                    ->label('Fermé ce jour')
                                    ->default(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('open', null);
                                            $set('close', null);
                                        }
                                    }),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un horaire')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['day'])
                                    ? (match($state['day']) {
                                        'monday' => 'Lundi',
                                        'tuesday' => 'Mardi',
                                        'wednesday' => 'Mercredi',
                                        'thursday' => 'Jeudi',
                                        'friday' => 'Vendredi',
                                        'saturday' => 'Samedi',
                                        'sunday' => 'Dimanche',
                                        default => $state['day']
                                    }) . (isset($state['closed']) && $state['closed'] ? ' (Fermé)' :
                                        (isset($state['open'], $state['close']) ? ' : ' . substr($state['open'], 0, 5) . ' - ' . substr($state['close'], 0, 5) : ''))
                                    : null
                            ),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Point de retrait')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PickupSlot $record): string => $record->address ?? ''),

                Tables\Columns\TextColumn::make('lat')
                    ->label('Latitude')
                    ->numeric(decimalPlaces: 6)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lng')
                    ->label('Longitude')
                    ->numeric(decimalPlaces: 6)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('working_hours')
                    ->label('Horaires')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Non défini';
                        }
                        $days = collect($state)->pluck('day')->map(function ($day) {
                            return match($day) {
                                'monday' => 'Lun',
                                'tuesday' => 'Mar',
                                'wednesday' => 'Mer',
                                'thursday' => 'Jeu',
                                'friday' => 'Ven',
                                'saturday' => 'Sam',
                                'sunday' => 'Dim',
                                default => $day
                            };
                        })->join(', ');
                        return $days;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Commandes')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('success'),

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
            ->defaultSort('name', 'asc');
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
