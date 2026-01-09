<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?string $modelLabel = 'Produit';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Laissez vide pour générer automatiquement'),

                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'legume' => 'Légume',
                                'volaille' => 'Volaille',
                                'autre' => 'Autre',
                            ])
                            ->required()
                            ->default('autre'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Prix et stock')
                    ->schema([
                        Forms\Components\TextInput::make('price_cents')
                            ->label('Prix (en centimes)')
                            ->required()
                            ->numeric()
                            ->suffix('centimes')
                            ->helperText('Ex: 250 pour 2.50€'),

                        Forms\Components\Select::make('unit')
                            ->label('Unité')
                            ->options([
                                'kg' => 'Kilogramme (kg)',
                                'piece' => 'Pièce',
                            ])
                            ->required()
                            ->default('piece'),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stock disponible')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Produit actif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image du produit')
                            ->image()
                            ->disk('r2')
                            ->directory('products')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(10240)
                            ->optimize('jpg')
                            ->resize(1920, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
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

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Catégorie')
                    ->colors([
                        'success' => 'legume',
                        'warning' => 'volaille',
                        'secondary' => 'autre',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'legume' => 'Légume',
                        'volaille' => 'Volaille',
                        'autre' => 'Autre',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('price_cents')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2, ',', ' ') . ' €')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unité')
                    ->formatStateUsing(fn ($state) => $state === 'kg' ? 'kg' : 'pièce'),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Actif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'legume' => 'Légume',
                        'volaille' => 'Volaille',
                        'autre' => 'Autre',
                    ]),

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
