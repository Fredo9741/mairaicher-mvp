<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $modelLabel = 'Commande';

    protected static ?string $pluralModelLabel = 'Commandes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations commande')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Numéro de commande')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'paid' => 'Payée',
                                'ready' => 'Prête',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('pickup_date')
                            ->label('Date de retrait')
                            ->disabled(),

                        Forms\Components\Select::make('pickup_slot_id')
                            ->label('Créneau de retrait')
                            ->relationship('pickupSlot', 'name')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Informations client')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nom')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Téléphone')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Montant')
                    ->schema([
                        Forms\Components\TextInput::make('total_price_cents')
                            ->label('Montant total (centimes)')
                            ->disabled()
                            ->suffix('centimes'),
                    ]),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes internes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Commande')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Téléphone')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'pending',
                        'success' => 'paid',
                        'info' => 'ready',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'ready' => 'Prête',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total_price_cents')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pickup_date')
                    ->label('Date retrait')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pickupSlot.name')
                    ->label('Créneau')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'ready' => 'Prête',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ]),

                Tables\Filters\Filter::make('pickup_date')
                    ->form([
                        Forms\Components\DatePicker::make('pickup_from')
                            ->label('Retrait du'),
                        Forms\Components\DatePicker::make('pickup_until')
                            ->label('Retrait jusqu\'au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['pickup_from'], fn ($q, $date) => $q->whereDate('pickup_date', '>=', $date))
                            ->when($data['pickup_until'], fn ($q, $date) => $q->whereDate('pickup_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Commande')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('Numéro'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'pending' => 'gray',
                                'paid' => 'success',
                                'ready' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'pending' => 'En attente',
                                'paid' => 'Payée',
                                'ready' => 'Prête',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('total_price_cents')
                            ->label('Montant total')
                            ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Créée le')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(2),

                Infolists\Components\Section::make('Client')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('customer_email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('customer_phone')
                            ->label('Téléphone'),
                    ])->columns(3),

                Infolists\Components\Section::make('Retrait')
                    ->schema([
                        Infolists\Components\TextEntry::make('pickup_date')
                            ->label('Date')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('pickupSlot.name')
                            ->label('Créneau'),
                    ])->columns(2),

                Infolists\Components\Section::make('Articles')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Produit')
                                    ->default(fn ($record) => $record->bundle?->name),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Quantité'),
                                Infolists\Components\TextEntry::make('price_at_purchase')
                                    ->label('Prix unitaire')
                                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €'),
                            ])
                            ->columns(3),
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
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
