<?php

namespace App\Filament\Resources;

use App\Enums\Animation;
use App\Enums\CardType;
use App\Enums\Effect;
use App\Enums\Keyword;
use App\Enums\Tribe;
use App\Enums\Trigger;
use App\Filament\Components\Columns\AttachmentColumn;
use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\CardResource\Pages;
use App\Models\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Card')->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),

                AttachmentInput::make('attachment_id')
                    ->label('Image')
                    ->required(),

                Select::make('artist_id')
                    ->label('Artist')
                    ->options(\App\Models\Artist::all()->pluck('name', 'id'))
                    ->required(),

                Grid::make()->schema([
                    Select::make('type')
                        ->options(CardType::list())
                        ->default('dude')
                        ->reactive()
                        ->required(),

                    Select::make('tribes')
                        ->options(Tribe::list())
                        ->helperText('"Ruse" is added automatically as a tribe if the card type is "ruse."')
                        ->hidden(fn (Get $get) => in_array($get('type'), ['artifact']))
                        ->multiple(),

                    TextInput::make('cost')
                        ->type('number')
                        ->default(1)
                        ->hidden(fn (Get $get) => in_array($get('type'), ['artifact']))
                        ->required(),

                    TextInput::make('power')
                        ->type('number')
                        ->default(100)
                        ->hidden(fn (Get $get) => in_array($get('type'), ['ruse', 'artifact']))
                        ->required(),
                ]),

                Select::make('sets')
                    ->relationship('sets', 'name')
                    ->multiple()
                    ->preload(),

                TextInput::make('enter_speed')
                    ->type('number')
                    ->default(500)
                    ->label('Enter speed')
                    ->helperText('The speed at which the card enters the screen, in milliseconds.')
                    ->required(),

                Grid::make()->schema(fn (Get $get) => [
                    Select::make('entrance_animation.animation')
                        ->options(Animation::list())
                        ->reactive()
                        ->nullable(),

                    ...Animation::tryFrom($get('entrance_animation.animation'))
                        ?->schema('entrance_animation.') ?? [],
                ]),
            ]),

            Section::make('Effects')->schema([
                Textarea::make('masked_text')
                    ->label('Masked text')
                    ->helperText(fn (null | Card $record) => new HtmlString(collect([
                            'This text will be displayed on the card instead of the auto-generated text.',
                            $record?->toText(),
                        ])->filter()->join('<br><br>')
                    )),

                Select::make('keywords')
                    ->options(Keyword::list())
                    ->multiple(),

                Repeater::make('effects')->collapsible()->schema([
                    Grid::make()->schema(fn (Get $get) => [
                        Select::make('trigger')
                            ->options(Trigger::list())
                            ->required()
                            ->reactive(),

                        ...Trigger::tryFrom($get('trigger'))?->schema() ?? [],

                        Select::make('effect')
                            ->options(Effect::list())
                            ->hidden(fn (Get $get) => Trigger::tryFrom($get('trigger'))?->hasEffectDropdown() === false)
                            ->required()
                            ->reactive(),
                    ]),

                    Grid::make()->schema(fn (Get $get) =>[
                        ...Effect::tryFrom($get('effect'))?->schema() ?? [],

                        Select::make('animation')
                            ->options(Animation::list())
                            ->nullable()
                            ->reactive(),

                        Grid::make()->schema([
                            ...Animation::tryFrom($get('animation'))?->schema() ?? [],
                        ]),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->sortable(),

                TextColumn::make('cost')
                    ->sortable(),

                AttachmentColumn::make('attachment_id')
                    ->label('Image'),

                TextColumn::make('sets')
                    ->getStateUsing(fn (Card $record) => $record->sets->pluck('name')->join(', ')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->filters([
                SelectFilter::make('sets')
                    ->relationship('sets', 'name')
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
