<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;   
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- BAGIAN KIRI (2/3 Kolom) ---
                Group::make([
                    Section::make("Post Details")
                        ->description("Fill in the details of the post")
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Group::make([
                                
                                TextInput::make('title')
                                    ->required()
                                    ->minLength(5)
                                    ->validationMessages([
                                        'min' => 'Judul minimal harus 5 karakter ya!',
                                        'required' => 'Judul jangan dikosongkan.',
                                    ]),

                                
                                TextInput::make('slug')
                                    ->required()
                                    ->minLength(3)
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Slug ini sudah ada yang punya, cari yang lain.',
                                        'min' => 'Slug kependekan, minimal 3 karakter.',
                                    ]),
                                
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                
                                ColorPicker::make('color'),
                            ])->columns(2),

                            MarkdownEditor::make('body')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(2),

                Group::make([
                    Section::make("Image Upload")
                        ->icon('heroicon-o-photo')
                        ->schema([
                            FileUpload::make("image")
                                ->required()
                                ->disk("public")
                                ->directory("posts"),
                        ]),

                    Section::make("Meta Information")
                        ->icon('heroicon-o-tag')
                        ->schema([
                            TagsInput::make('tags'),
                            Checkbox::make('published'),
                            DateTimePicker::make('published_at'),
                        ]),
                ])->columnSpan(1),

            ])->columns(3);
    }
}