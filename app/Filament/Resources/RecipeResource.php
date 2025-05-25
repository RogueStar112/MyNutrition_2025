<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use App\Models\Food;
use App\Models\Meal;
use App\Models\FoodSource;
use App\Models\FoodUnit;
use App\Models\Macronutrients;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;

use Illuminate\Support\HtmlString;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;

use Filament\Forms\Get;

use Filament\Actions\Action;

use Filament\Forms\Components\Checkbox;

use Filament\Forms\Components\Textarea;

use Filament\Forms\Components\MarkdownEditor;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Step 1. Name and Source')
                ->schema([
                    TextInput::make('name')
                    ->label('Recipe Name')
                    ->helperText('The name of the recipe you want to add.')
                    ->placeholder('Spaghetti Bolognese')
                    ->markAsRequired()
                    ->required(false)
                    ->maxLength(64),


                    TextInput::make('subheading')
                    ->label('Subheading')
                    ->helperText('A brief description of this recipe.')
                    ->placeholder('A simple, delicious classic pasta dish.')
                    ->markAsRequired()
                    ->required(false)
                    ->maxLength(128),
                ]),

                Fieldset::make('Step 2. Tags')
                ->schema([
                    TagsInput::make('tags')
                    ->label('Tag Name')
                    ->helperText('The name of the tag you want to add.')
                    ->placeholder('pasta, italian')
                    ->splitKeys(['Tab', ' ', ','])
                    ->suggestions([
                        'food',
                        'drink',
                        'pizza',
                        'pasta',
                        'italian',
                        'american',
                        'british',
                        'filipino',
                        'thai',
                        'vietnamese',
                        'japanese',
                        'indian',
                        'healthy',
                        'low-carb',
                        'high-carb',
                        'weight-gain',
                        'low-fat',
                        'high-fat',
                        'low-protein',
                        'high-protein',
                        'vegetarian',
                        'vegan',
                        'non-vegetarian',
                    ])
                    ->markAsRequired()
                    ->required(false),


                    // ColorPicker::make('color_bg')
                    // ->label('Tag Background Colour')
                    // ->default('#000000'),

                    // ColorPicker::make('color_text')
                    // ->label('Tag Text Colour')
                    // ->default('#F59E0B'),

                ])
                ->columns(1),

                Fieldset::make('Step 3. Foods in this recipe')
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
                                            ->markAsRequired()
                                            ->required(false),

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
                                                ->markAsRequired()
                                                ->required(false),


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
                                        ->label('User')
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
                                        ->columnSpan(2)
                                        ->required(),

                                        MarkdownEditor::make('description')
                                        ->columnSpan(2),

                                        Checkbox::make('is_public')
                                        ->label('Make recipe visible to public'),

                                    
                                        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                            ->searchable(),

                Tables\Columns\TextColumn::make('subheading'),
                
                 Tables\Columns\TextColumn::make('user.name')
                                    ->searchable(),
        
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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}