<?php

namespace App\Filament\Resources\AlternateArtResource\Pages;

use App\Filament\Resources\AlternateArtResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlternateArt extends EditRecord
{
    protected static string $resource = AlternateArtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
