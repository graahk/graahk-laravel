<?php

namespace App\Filament\Resources;

use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\AlternateArtResource\Pages;
use App\Models\AlternateArt;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AlternateArtResource extends Resource
{
    protected static ?string $model = AlternateArt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Card')->schema([
                Select::make('card_id')
                    ->label('Card')
                    ->options(\App\Models\Card::all()->pluck('name', 'id')->sort())
                    ->searchable()
                    ->required(),

                Select::make('artist_id')
                    ->label('Artist')
                    ->options(\App\Models\Artist::all()->pluck('name', 'id'))
                    ->required(),

                Toggle::make('in_packs')
                    ->label('Included in packs?'),
            ]),

            Repeater::make('attachments')->columnSpanFull()->columns(1)->schema([
                TextInput::make('depth')
                    ->label('Depth (0-100, frame is 50)')
                    ->default(0)
                    ->required(),

                AttachmentInput::make('attachment')
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

                ToggleColumn::make('in_packs')
                    ->label('In packs')
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
            'index' => Pages\ListAlternateArt::route('/'),
            'create' => Pages\CreateAlternateArt::route('/create'),
            'edit' => Pages\EditAlternateArt::route('/{record}/edit'),
        ];
    }
}
