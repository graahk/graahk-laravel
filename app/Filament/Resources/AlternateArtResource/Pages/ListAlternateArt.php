<?php

namespace App\Filament\Resources\AlternateArtResource\Pages;

use App\Filament\Resources\AlternateArtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlternateArt extends ListRecords
{
    protected static string $resource = AlternateArtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
