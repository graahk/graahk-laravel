<?php

namespace App\Filament\Resources\BossResource\Pages;

use App\Filament\Resources\BossResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoss extends EditRecord
{
    protected static string $resource = BossResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
