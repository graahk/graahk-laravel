<?php

namespace App\Filament\Resources\AvatarBorderResource\Pages;

use App\Filament\Resources\AvatarBorderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvatarBorders extends ListRecords
{
    protected static string $resource = AvatarBorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
