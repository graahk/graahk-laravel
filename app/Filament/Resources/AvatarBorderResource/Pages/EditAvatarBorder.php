<?php

namespace App\Filament\Resources\AvatarBorderResource\Pages;

use App\Filament\Resources\AvatarBorderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvatarBorder extends EditRecord
{
    protected static string $resource = AvatarBorderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
