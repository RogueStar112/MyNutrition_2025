<?php

namespace App\Filament\Resources\MealResource\Widgets;

use Filament\Widgets\ChartWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use Carbon\Carbon;

use Auth;

use App\Models\Food;
use App\Models\Macronutrients;
use App\Models\Meal;
use App\Models\MealItems;
use App\Models\FoodUnit;

class DailyCaloriesChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Macros (Last 30 Days)';

    protected function getData(): array
    {
        $meals = Meal::with('mealItems')
            ->whereBetween('time_planned', [now()->subDays(30), now()])
            ->get();



        foreach($meals as $key => $value) {
            
            // dd($value);
            
            $meal_calories = 0;
            $meal_fats = 0;
            $meal_carbs = 0;
            $meal_protein = 0;

            $mealItems = MealItems::where('meal_id', $value->id)
                            ->get();

                            
            // $meal_calories = 0;

            foreach($mealItems as $mealItem) {

                $mealItem_macro = Macronutrients::where('food_id', $mealItem->food_id)
                                        ->first();

                // dd($mealItem_macro);

                $meal_calories += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->calories, 0);
                $meal_fats += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->fat, 1);
                $meal_carbs += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->carbohydrates, 1);
                $meal_protein += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->protein, 1);

                $meals[$key]['calories'] = $meal_calories;
                $meals[$key]['fat'] = $meal_fats;
                $meals[$key]['carbs'] = $meal_carbs;
                $meals[$key]['protein'] = $meal_protein;

            }

            // dd($meals);



        // dd($meals);

        }

        // dd($meals);

                    // dd($meal_calories, $meal_fats, $meal_carbs, $meal_protein);
        
        // dd($meals);

        $trendData = $meals->groupBy(fn($meal) => date('Y-m-d', strtotime($meal->time_planned)))
            ->map(function ($mealsOnDate) {
                $totals = [
                    'calories' => 0,
                    'protein' => 0,
                    'carbs' => 0,
                    'fat' => 0,
                ];
                
                // dd($mealsOnDate);

                foreach ($mealsOnDate as $meal) {
                        $macro = $meal;
                        if ($macro) {
                            // $multiplier = $item->quantity / $macro->serving_size;
                            $totals['calories'] += $macro->calories;
                            $totals['protein']  += $macro->protein; 
                            $totals['carbs']    += $macro->carbs; 
                            $totals['fat']      += $macro->fat;
                        }
                    }

    

                return $totals;
            });

        // Format for Filament chart
        
        // dd($trendData);

        $labels = $trendData->keys()->toArray();
        

        // set to dd/mm/yyyy format
        foreach ($labels as $key => $label) {
            $label = date('d M', strtotime($label));

            $labels[$key] = $label;
            
        }
        // dd($labels);

        return [
            'datasets' => [
                [
                    'label' => 'Calories',
                    'data' => $trendData->pluck('calories')->map(fn($val) => round($val))->toArray(),
                    'borderColor' => '#f87171', // Red
                ],

                [
                    'label' => 'Fat',
                    'data' => $trendData->pluck('fat')->map(fn($val) => round($val))->toArray(),
                    'borderColor' => '#fbbf24', // Yellow
                ],

                
                [
                    'label' => 'Carbs',
                    'data' => $trendData->pluck('carbs')->map(fn($val) => round($val))->toArray(),
                    'borderColor' => '#60a5fa', // Blue
                ],
                
                [
                    'label' => 'Protein',
                    'data' => $trendData->pluck('protein')->map(fn($val) => round($val))->toArray(),
                    'borderColor' => '#34d399', // Green
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
