<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BenefitSlideResource\Pages;
use App\Models\BenefitSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BenefitSlideResource extends Resource
{
    protected static ?string $model = BenefitSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slide Content')
                    ->description('What visitors see in the homepage benefits carousel.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->minLength(2)
                            ->maxLength(60)
                            ->unique(ignoreRecord: true)
                            ->helperText('Short and punchy — it wraps onto two lines on the homepage, so keep it brief.'),
                        Forms\Components\MarkdownEditor::make('body_text')
                            ->label('Body text')
                            ->required()
                            ->minLength(10)
                            ->maxLength(600)
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                                'table',
                                'blockquote',
                                'heading',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Display Order')
                    ->description('Where this slide appears relative to the others.')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->default(fn () => (BenefitSlide::max('sort_order') ?? 0) + 1)
                            ->helperText('Lower numbers show first. Other slides shift automatically to keep every position unique — you can also drag rows to reorder in the table below.')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Drag-and-drop reordering. Filament's CanReorderRecords already
            // wraps this in DB::transaction, updating every row's sort_order
            // atomically so a mid-drag failure can't leave slides half-reordered.
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body_text')
                    ->label('Body text')
                    ->formatStateUsing(fn (string $state): string => Str::of($state)->markdown()->stripTags()->squish()->limit(60)),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            DB::transaction(function () use ($records): void {
                                $records->each->delete();
                            });
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBenefitSlides::route('/'),
            'create' => Pages\CreateBenefitSlide::route('/create'),
            'edit' => Pages\EditBenefitSlide::route('/{record}/edit'),
        ];
    }
}
