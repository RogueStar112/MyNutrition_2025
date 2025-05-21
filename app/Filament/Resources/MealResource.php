<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealResource\Pages;
use App\Filament\Resources\MealResource\RelationManagers;


use App\Models\Food;
use App\Models\Meal;
use App\Models\FoodSource;
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


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected ?string $subheading = 'Custom Page Subheading';

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

                        DateTimePicker::make('time_planned')
                        ->label('Meal Time')
                        ->helperText(str("When you're going to have your meal. If in future time, it's considered a planned meal.")->inlineMarkdown()->toHtmlString())
                        ->columnSpan(1)
                        ->seconds(false)
                        ->required(),
                    ]),

                Fieldset::make('Step 2. Foods in this meal')
                    ->schema([
                        Repeater::make('food')
                           ->schema([
                                        Forms\Components\Select::make('name')
                                            ->label('Food name')
                                            ->options(Food::all()->pluck('name', 'id')->reverse())
                                            ->searchable()
                                            // ->multiple()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state != NULL) {
                                                    $servingUnit = FoodUnit::find(
                                                        Macronutrients::where('food_id', Food::find($state)->id)->first()?->food_unit_id)
                                                        ?->name ?? '';
                                                    $foodSource = FoodSource::find(Food::find($state)->source_id)->name;
                                                    $foodId = $state;
                                                    $set('source_name', $foodSource);
                                                    $set('calories', 0);
                                                    $set('fat', 0);
                                                    $set('carbs', 0);
                                                    $set('protein', 0);
                                                    $set('food_id', $foodId);
                                                    $set('serving_unit_label', $servingUnit);
                                                }
                                            })
                                            ->helperText(fn (Get $get) => ('From ' . $get('source_name') ?? ''))
                                            ->required(),

                                            Forms\Components\TextInput::make('serving_size')
                                                ->label(fn (Get $get) => 'Serving size: ' . ($get('serving_unit_label') ?? ''))
                                                ->suffix(fn (Get $get) => ($get('serving_unit_label') . "s" ?? ''))
                                                 ->live(debounce: 500)
                                                  ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                                $calories =
                                                     round(Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->calories * ($state / Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->serving_size), 1)  ?? '';
                                                $fat =
                                                     round(Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->fat * ($state / Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->serving_size), 1)  ?? '';
                                                $carbs =
                                                     round(Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->carbohydrates * ($state / Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->serving_size), 1)  ?? '';
                                                $protein =
                                                    round(Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->protein * ($state / Macronutrients::where('food_id', Food::find($get('food_id') ?? '')->id)->first()
                                                    ?->serving_size), 1)  ?? '';

                                                $set('calories', $calories);
                                                $set('fat', $fat);
                                                $set('carbs', $carbs);
                                                $set('protein', $protein);
                                                })
                                                // ->helperText(fn (Get $get) => ('This has ' . $get('calories') . " calories." ?? ''))
                                                ->disabled(fn (callable $get) => blank($get('food_id')))
                                                ->required(),


                                             Fieldset::make('Nutrition info: (Display only)')
                                            ->schema([
                                                Forms\Components\TextInput::make('calories')
                                                ->disabled(),
                                                
                                                Forms\Components\TextInput::make('fat')
                                                ->disabled(),
                                                
                                                Forms\Components\TextInput::make('carbs')
                                                ->disabled(),
                                                
                                                Forms\Components\TextInput::make('protein')    
                                                ->disabled(),
                                            ])
                                            ->columns(4)

                                            // Forms\Components\TextInput::make('quantity')
                                            // ->required(),
                                ])
                                    ->reactive()
                                    ->columnSpan(2)
                    ->columns(3)

                                        ]),
                         Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->required()
                        ->password()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->maxLength(255)
                ])
                ->required(),

                    ])

                    
            ->columns(3);

    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //        ->defaultSort('id', 'desc')
    //         ->columns([
    //             Tables\Columns\TextColumn::make('name'),

    //             Tables\Columns\TextColumn::make('time_planned')
    //             ->dateTime(),

    //             Tables\Columns\TextColumn::make('created_at')
    //             ->dateTime()
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    
    public static function getWidgets(): array {
        return [
            MealResource\Widgets\DailyCaloriesChart::class,
            // MealResource\Widgets\MacroStats::class,
            MealResource\Widgets\StatsOverview::class,
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
