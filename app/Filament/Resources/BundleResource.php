<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BundleResource\Pages;
use App\Models\Bundle;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BundleResource extends Resource
{
    protected static ?string $model = Bundle::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Paniers';

    protected static ?string $modelLabel = 'Panier';

    protected static ?string $pluralModelLabel = 'Paniers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du panier')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Laissez vide pour générer automatiquement'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price_cents')
                            ->label('Prix de vente')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->suffix('€')
                            ->placeholder('0.00')
                            ->helperText('Entrez le prix en euros (ex: 15.00)')
                            // Divise par 100 pour afficher en euros
                            ->formatStateUsing(fn ($state): ?string => $state ? number_format($state / 100, 2, '.', '') : null)
                            // Multiplie par 100 et arrondit avant d'enregistrer
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) round($state * 100) : 0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Panier actif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Composition du panier')
                    ->schema([
                        Forms\Components\Textarea::make('composition_indicative')
                            ->label('Composition indicative du panier')
                            ->placeholder('Ex: 5kg de légumes de saison (tomates, salades, courgettes...)')
                            ->rows(4)
                            ->helperText('Décrivez simplement le contenu du panier pour vos clients')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantité disponible')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Nombre de paniers en stock')
                            ->required(),
                    ])->columns(1),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image du panier')
                            ->image()
                            ->disk('r2')
                            ->directory('bundles')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(10240)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('r2')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_cents')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 10 ? 'success' : ($state > 5 ? 'warning' : ($state > 0 ? 'danger' : 'gray'))),

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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBundles::route('/'),
            'create' => Pages\CreateBundle::route('/create'),
            'edit' => Pages\EditBundle::route('/{record}/edit'),
        ];
    }
}
