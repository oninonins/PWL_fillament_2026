<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Forms\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // Step 1
                    Wizard\Step::make('Product Info')
                        ->icon('heroicon-m-information-circle')
                        ->schema([
                            FormGrid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('sku')
                                        ->label('SKU')
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                ]),
                            MarkdownEditor::make('description')
                                ->columnSpanFull(),
                        ]),

                    // Step 2
                    Wizard\Step::make('Pricing & Stock')
                        ->icon('heroicon-m-banknotes')
                        ->schema([
                            FormGrid::make(2)
                                ->schema([
                                    TextInput::make('price')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->prefix('Rp'),
                                    TextInput::make('stock')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0),
                                ]),
                        ]),

                    // Step 3
                    Wizard\Step::make('Media & Status')
                        ->icon('heroicon-m-photo')
                        ->schema([
                            FileUpload::make('image')
                                ->disk('public')
                                ->directory('products')
                                ->columnSpanFull(),
                            FormGrid::make(2)
                                ->schema([
                                    Checkbox::make('is_active')
                                        ->default(true),
                                    Checkbox::make('is_featured'),
                                ]),
                        ]),
                ])
                ->skippable()
                ->columnSpanFull()
                ->submitAction(
                    Action::make('save')
                        ->label('Save Product')
                        ->submit('save')
                        ->color('primary')
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('IDR'),
                TextColumn::make('stock')
                    ->numeric(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Details')
                    ->tabs([
                        Tabs\Tab::make('Product Info')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                InfolistGrid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->weight('bold'),
                                        TextEntry::make('sku')
                                            ->badge()
                                            ->color('info'),
                                    ]),
                                TextEntry::make('description')
                                    ->markdown(),
                            ]),
                        Tabs\Tab::make('Pricing & Stock')
                            ->icon('heroicon-m-banknotes')
                            ->badge(fn ($record) => $record->stock)
                            ->badgeColor(fn ($record) => $record->stock < 10 ? 'danger' : 'success')
                            ->schema([
                                InfolistGrid::make(2)
                                    ->schema([
                                        TextEntry::make('price')
                                            ->label('Price')
                                            ->formatStateUsing(fn ($state): string => 'Rp ' . number_format($state, 0, ',', '.')),
                                        TextEntry::make('stock')
                                            ->icon('heroicon-m-circle-stack')
                                            ->numeric(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Media & Status')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                ImageEntry::make('image')
                                    ->disk('public')
                                    ->columnSpanFull(),
                                InfolistGrid::make(2)
                                    ->schema([
                                        IconEntry::make('is_active')
                                            ->boolean(),
                                        IconEntry::make('is_featured')
                                            ->boolean(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
