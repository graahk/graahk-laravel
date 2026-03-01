<?php

namespace App\Filament\Resources;

use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\AvatarBorderResource\Pages;
use App\Models\AvatarBorder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AvatarBorderResource extends Resource
{
    protected static ?string $model = AvatarBorder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->required(),

                AttachmentInput::make('attachment_id')
                    ->label('Attachment')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('card.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('artist.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAvatarBorders::route('/'),
            'create' => Pages\CreateAvatarBorder::route('/create'),
            'edit' => Pages\EditAvatarBorder::route('/{record}/edit'),
        ];
    }
}
