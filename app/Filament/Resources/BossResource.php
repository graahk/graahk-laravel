<?php

namespace App\Filament\Resources;

use App\Enums\BossType;
use App\Filament\Components\Columns\AttachmentColumn;
use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\BossResource\Pages;
use App\Models\Boss;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BossResource extends Resource
{
    protected static ?string $model = Boss::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Select::make('boss_type')
                    ->options(BossType::list())
                    ->reactive()
                    ->required(),

                AttachmentInput::make('attachment_id')
                    ->label('Image')
                    ->required(),

                Select::make('artist_id')
                    ->relationship('artist', 'name')
                    ->label('Artist')
                    ->nullable(),

                Grid::make()->schema([
                    TextInput::make('energy_gain')
                        ->type('number')
                        ->default(3)
                        ->required(),

                    TextInput::make('power')
                        ->type('number')
                        ->default(100)
                        ->required(),
                ]),

                Select::make('boss_artifacts')
                    ->label('Artifacts')
                    ->multiple()
                    ->relationship('artifacts', 'name')
                    ->preload()
                    ->searchable()
                    ->options(\App\Models\Card::where('type', 'artifact')->pluck('name', 'id')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('boss_type')
                    ->sortable(),

                TextColumn::make('energy_gain')
                    ->sortable(),

                TextColumn::make('power')
                    ->sortable(),

                AttachmentColumn::make('attachment_id')
                    ->label('Image'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBosses::route('/'),
            'create' => Pages\CreateBoss::route('/create'),
            'edit' => Pages\EditBoss::route('/{record}/edit'),
        ];
    }
}
