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

use App\Models\FoodSource;
use App\Models\Macronutrients;
use App\Models\Micronutrients;

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
        

                Forms\Components\TextInput::make('serving_size')
                ->placeholder('100')
                ->required(),

                // food units are arbitrarily preloaded in the database, and is currently simplified.
                Forms\Components\Select::make('food_unit')
                ->options([
                    '1' => 'grams (g)',
                    '5' => 'pieces (pcs)',
                    '6' => 'portions (x)',
                    '7' => 'slice (slices)',
                    '8' => 'tablespoons (tbsp)',
                    '9' => 'teaspoons (tsp)',
                    '10' => 'mililitres (ml)',
                    '12' => 'litres (l)'
                ])
                ->required(),

                            // Macronutrient fields
            Forms\Components\TextInput::make('calories')
                ->label('Calories (kcal)')
                ->numeric()
                ->required()
                ->default(fn ($record) => $record?->macronutrients?->calories) // <-- THIS
                ->dehydrateStateUsing(fn ($state) => (float) $state), // Save clean

            Forms\Components\TextInput::make('fat')
                ->label('Fat (g)')
                ->numeric()
                ->required()
                ->default(fn ($record) => $record?->macronutrients?->fat)
                ->dehydrateStateUsing(fn ($state) => (float) $state), // Save clean

            Forms\Components\TextInput::make('carbohydrates')
                ->label('Carbohydrates (g)')
                ->numeric()
                ->required()
                ->default(fn ($record) => $record?->macronutrients?->carbohydrates)
                ->dehydrateStateUsing(fn ($state) => (float) $state), // Save clean


            Forms\Components\TextInput::make('protein')
                ->label('Protein (g)')
                ->numeric()
                ->required()
                ->default(fn ($record) => $record?->macronutrients?->protein)
                ->dehydrateStateUsing(fn ($state) => (float) $state), // Save clean

        
                
            
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

    public static function create(): CreateRecord
    {

    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(['name', 'id']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('source.name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at'),

                Tables\Columns\TextColumn::make('macronutrients.calories')
                ->label('Calories'),

                             
                Tables\Columns\TextColumn::make('macronutrients.carbohydrates')
                ->label('Carbs (g)'),

                
                Tables\Columns\TextColumn::make('macronutrients.fat')
                ->label('Fat (g)'),

                
                Tables\Columns\TextColumn::make('macronutrients.protein')
                ->label('Protein (g)'),
                // Tables\Columns\TextColumn::make('updated_at'),

                Tables\Columns\TextColumn::make('user.name')
                    
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
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

    protected function afterSave(): void
    {
    $macronutrients = $this->record->macronutrients;

    if ($macronutrients) {
        $macronutrients->update([
            'food_unit_id' => $this->form->getState()['food_unit'],
            'serving_size' => $this->form->getState()['serving_size'],
            'calories' => $this->form->getState()['calories'],
            'fat' => $this->form->getState()['fat'],
            'carbohydrates' => $this->form->getState()['carbohydrates'],
            'protein' => $this->form->getState()['protein'],
        ]);
    } else {
        // If Macronutrients doesn't exist, create one
        Macronutrients::create([
            'food_id' => $this->record->id,
            'food_unit_id' => $this->form->getState()['food_unit'],
            'serving_size' => $this->form->getState()['serving_size'],
            'calories' => $this->form->getState()['calories'],
            'fat' => $this->form->getState()['fat'],
            'carbohydrates' => $this->form->getState()['carbohydrates'],
            'protein' => $this->form->getState()['protein'],
        ]);
    }

    // --- 🎉 FANCY SUCCESS TOAST ---
    $this->notify('success', 'Macronutrients saved successfully!');
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
