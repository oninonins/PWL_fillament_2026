<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // Fungsi ini akan menghilangkan tombol "Create" default yang bikin error
    protected function getFormActions(): array
    {
        return [];
    }
}