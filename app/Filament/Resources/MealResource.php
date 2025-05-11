<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealResource\Pages;
use App\Filament\Resources\MealResource\RelationManagers;


use App\Models\Food;
use App\Models\Meal;
use App\Models\FoodUnit;
use App\Models\Macronutrients;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;

use Filament\Forms\Get;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;

class MealResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                 Fieldset::make('Step 1. Meal name and time')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        
                        ->label('Meal Name')
                        ->placeholder('Grilled Cheese Sandwich')
                        ->required()
                        ->helperText(str('The name of your meal.')->inlineMarkdown()->toHtmlString())
                        ->maxLength(64),

                        DateTimePicker::make('meal_time')
                        ->helperText(str("When you're going to have your meal. If in future time, it's considered a planned meal.")->inlineMarkdown()->toHtmlString())
                        ->columnSpan(1)
                        ->required(),
                    ]),

                Fieldset::make('Step 2. Foods in this meal')
                    ->schema([
                        Repeater::make('food')
                           ->schema([
                                        Forms\Components\Select::make('food_name')
                                            ->label('Food name')
                                            ->options(Food::all()->pluck('name', 'id'))
                                            ->searchable()
                                            // ->multiple()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $servingUnit = FoodUnit::find(Macronutrients::find($state)?->food_unit_id)?->name ?? '';
                                                $set('serving_unit_label', $servingUnit);
                                            })
                                            ->required(),

                                            Forms\Components\TextInput::make('serving_size')
                                                ->label(fn (Get $get) => 'Serving size: ' . ($get('serving_unit_label') ?? ''))
                                                ->required(),

                                            Forms\Components\TextInput::make('quantity')
                                            ->required(),
                                ])
                                    ->columnSpan(2)
                    ->columns(3)

                        ])
                    ])
            ->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            'index' => Pages\ListMeals::route('/'),
            'create' => Pages\CreateMeal::route('/create'),
            'edit' => Pages\EditMeal::route('/{record}/edit'),
        ];
    }
}
