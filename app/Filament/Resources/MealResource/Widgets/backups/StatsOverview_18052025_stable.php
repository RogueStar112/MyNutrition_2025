<?php

namespace App\Filament\Resources\MealResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use Carbon\Carbon;

use Auth;

use App\Models\Food;
use App\Models\Macronutrients;
use App\Models\Meal;
use App\Models\MealItems;
use App\Models\FoodUnit;

use Illuminate\Support\Facades\DB;


class StatsOverview_test extends BaseWidget
{   
    protected ?string $heading = 'Analytics';

    protected function getStats(): array
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

        $labels = $trendData->keys()->toArray();
        

        // set to dd/mm/yyyy format
        foreach ($labels as $key => $label) {
            $label = date('d M', strtotime($label));

            $labels[$key] = $label;
            
        }

        $avg_calories_mspd = $trendData->pluck('calories')->map(fn($val) => round($val))->toArray();

        $avg_calories_mspd = round(array_sum($avg_calories_mspd) / count($avg_calories_mspd), 0);

        // dd($avg_calories_mspd);

        $popular_meal_items = DB::table('meal_items')
            ->join('meal', 'meal_items.meal_id', '=', 'meal.id')
            ->where('meal.user_id', Auth::id())
            ->whereBetween('meal.time_planned', [now()->subDays(6), now()])
            ->select('meal_items.name', DB::raw('count(*) as total'))
            ->groupBy('meal_items.name')
            ->orderByDesc('total')
            ->limit(3)
            ->get();
        // dd($popular_meal_items);

        $last7days = date('d/m/Y', strtotime(now()->subdays(6)));
        $today = date('d/m/Y', strtotime(now()));

        $popular_meal_items_display = implode(", ", $popular_meal_items->pluck('name')->map(fn($val) => $val)->toArray());
        


        return [
        Stat::make('Average Calories Per Day', "$avg_calories_mspd" . "kcal")
            ->description('On 12/05/2025')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart($trendData->pluck('calories')->map(fn($val) => round($val))->toArray())
            ->color('success'),
        Stat::make('Top 3 Popular Meal Items this week', "$popular_meal_items_display")
            ->description("From $last7days to $today"),
        Stat::make('Top 3 Recent Meal Items by Macro (Fat/Carbs/Protein)', "")
            ->description("Fat")
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
            ]),
        ];
    }
}
