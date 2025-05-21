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

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;

use Illuminate\Support\HtmlString;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;

use Filament\Forms\Components\ColorPicker;


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
                    ->label('Name')
                    ->helperText('The name of the recipe you want to add.')
                    ->placeholder('Spaghetti Bolognese')
                    ->required()
                    ->maxLength(64),


                    TextInput::make('subheading')
                    ->label('Subheading')
                    ->helperText('A brief description of this recipe.')
                    ->placeholder('A simple, delicious classic pasta dish.')
                    ->required()
                    ->maxLength(128),
                ]),

                Fieldset::make('Step 2. Tags')
                ->schema([
                    TagsInput::make('tags')
                    ->label('Name')
                    ->helperText('The name of the tag you want to add.')
                    ->placeholder('pasta, italian')
                    ->splitKeys(['Tab', ' '])
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
                    ->required(),


                    // ColorPicker::make('color_bg')
                    // ->label('Tag Background Colour')
                    // ->default('#000000'),

                    // ColorPicker::make('color_text')
                    // ->label('Tag Text Colour')
                    // ->default('#F59E0B'),

                ])
                ->columns(1),
            ]);
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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
