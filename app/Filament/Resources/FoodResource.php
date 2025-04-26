<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->placeholder('Ricotta Cheese')
                ->required()
                ->maxLength(64),


                Forms\Components\TextInput::make('food_source')
                ->placeholder('Aldi')
                ->required()
                ->maxLength(64),
        

                Forms\Components\TextInput::make('food_quantity')
                ->required(),

                // food units are arbitrarily preloaded in the database, and is currently simplified.
                Forms\Components\Select::make('food_unit')
                ->options([
                    '1' => 'grams (g)',
                    '6' => 'portions (x)',
                    '5' => 'pieces (pcs)',
                    '7' => 'slice (slices)',
                    '8' => 'tablespoons (tbsp)',
                    '9' => 'teaspoons (tsp)',
                    '10' => 'mililitres (ml)',
                    '12' => 'litres (l)'
                ])
                ->required(),

                Forms\Components\TextInput::make('calories')
                ->label('Calories (kcal)')
                ->required(),

                Forms\Components\TextInput::make('fat')
                ->label('Fat (g)')
                ->required(),

                Forms\Components\TextInput::make('carbohydrates')
                ->label('Carbohydrates (g)')
                ->required(),

                Forms\Components\TextInput::make('protein')
                ->label('Protein (g)')
                ->required(),

        
                
            
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

                Forms\Components\TextInput::make('description')
                ->maxLength(256),
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }
}
