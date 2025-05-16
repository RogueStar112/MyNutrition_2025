<?php

namespace App\Filament\Resources\MealResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use App\Models\Food;
use App\Models\Meal;


use App\Models\MealItems;


use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class MacroStats extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            // ->query(fn () => Meal::query()
            
            
            //     )

             ->query(function () {
                return MealItems::query()
                    ->join('meal', 'meal_items.meal_id', '=', 'meal.id')
                    ->select('meal.*', 'meal_items.*');
            })
            ->columns([
                TextColumn::make('meal.name'),
            
                // TextColumn::make('macronutrients.calories'),
                // TextColumn::make('macronutrients.fat'),
                // TextColumn::make('macronutrients.carbohydrates'),
                // TextColumn::make('macronutrients.protein')

            ]);
    }
}
