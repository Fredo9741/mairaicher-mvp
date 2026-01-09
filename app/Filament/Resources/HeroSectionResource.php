<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSectionResource\Pages;
use App\Filament\Resources\HeroSectionResource\RelationManagers;
use App\Models\HeroSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeroSectionResource extends Resource
{
    protected static ?string $model = HeroSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Hero Section';

    protected static ?string $modelLabel = 'Hero Section';

    protected static ?string $pluralModelLabel = 'Hero Sections';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Contenu du site';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image Hero')
                    ->description('Image principale de la page d\'accueil (optimisée automatiquement en WebP)')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image du Hero')
                            ->image()
                            ->disk('r2')
                            ->directory('hero')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                            ])
                            ->maxSize(10240)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Utilisez l\'éditeur pour redimensionner. Max 10MB.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Contenu textuel')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre principal')
                            ->required()
                            ->maxLength(255)
                            ->default('Domaine des Papangues'),

                        Forms\Components\Textarea::make('subtitle')
                            ->label('Sous-titre')
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->default('Domaine des Papangues : agriculture, élevage biologique et traditionnel'),

                        Forms\Components\TextInput::make('badge_text')
                            ->label('Texte du badge')
                            ->required()
                            ->maxLength(255)
                            ->default('Production locale & Agriculture durable'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Section active')
                            ->helperText('Seule une section peut être active à la fois')
                            ->default(true),
                    ])->columns(1),
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

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('badge_text')
                    ->label('Badge')
                    ->limit(30),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('Tous')
                    ->trueLabel('Actives seulement')
                    ->falseLabel('Inactives seulement'),
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
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListHeroSections::route('/'),
            'create' => Pages\CreateHeroSection::route('/create'),
            'edit' => Pages\EditHeroSection::route('/{record}/edit'),
        ];
    }
}
