<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    // Fungsi agar kembali ke tabel setelah klik Create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}