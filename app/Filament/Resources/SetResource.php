<?php

namespace App\Filament\Resources;

use App\Filament\Components\Columns\AttachmentColumn;
use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\SetResource\Pages;
use App\Models\Set;
use Filament\Forms\Components\Grid;
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

class SetResource extends Resource
{
    protected static ?string $model = Set::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->placeholder('Name'),

                TextInput::make('code')
                    ->required(),

                Toggle::make('beta')
                    ->label('Is in beta?'),

                Toggle::make('artifacts_set')
                    ->label('Is an artifacts set?'),

                Grid::make()->schema([
                    AttachmentInput::make('attachment_id')
                        ->label('Cover image'),

                    AttachmentInput::make('icon_id')
                        ->label('Icon')
                        ->required(),
                ]),
            ]),

            Section::make('Cards')->schema([
                Select::make('cards')
                    ->relationship('cards', 'name')
                    ->multiple()
                    ->searchable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('beta')
                    ->label('Is in beta?'),

                ToggleColumn::make('artifacts_set')
                    ->label('Is an artifacts set?'),

                AttachmentColumn::make('icon_id')
                    ->label('Icon'),
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
            'index' => Pages\ListSets::route('/'),
            'create' => Pages\CreateSet::route('/create'),
            'edit' => Pages\EditSet::route('/{record}/edit'),
        ];
    }
}
