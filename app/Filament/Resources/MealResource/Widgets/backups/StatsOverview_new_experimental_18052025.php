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
use App\Models\Micronutrients;
use App\Models\Meal;
use App\Models\MealItems;
use App\Models\FoodUnit;

use Illuminate\Support\Facades\DB;


class StatsOverview_experimental extends BaseWidget
{   
    protected ?string $heading = 'Analytics';

    protected function getStats(): array
    {
        $meals = Meal::with('mealItems')
            ->whereBetween('time_planned', [now()->subDays(30), now()])
            ->get();

        
        $mealsData = [];

            foreach ($meals as $key => $meal) {

                // Macros
                $meal_calories = 0;
                $meal_fats = 0;
                $meal_carbs = 0;
                $meal_protein = 0;

                // Micros
                $meal_sugars = 0;
                $meal_saturates = 0;
                $meal_fibre = 0;
                $meal_salt = 0;

                $mealItems = MealItems::where('meal_id', $meal->id)->get();

                foreach ($mealItems as $key_meal => $mealItem) {
                    $macro = Macronutrients::where('food_id', $mealItem->food_id)->first();

                    $calories = round(($mealItem->serving_size / $macro->serving_size) * $macro->calories, 0);
                    $fat = round(($mealItem->serving_size / $macro->serving_size) * $macro->fat, 1);
                    $carbs = round(($mealItem->serving_size / $macro->serving_size) * $macro->carbohydrates, 1);
                    $protein = round(($mealItem->serving_size / $macro->serving_size) * $macro->protein, 1);

                    $micro = Micronutrients::where('food_id', $mealItem->food_id)?->first();
                    
                    if(!$micro) {

                    } else {

           

                        $sugars = round(($mealItem->serving_size / $macro->serving_size) * $micro->sugars ?? 0, 1);
                        $saturates = round(($mealItem->serving_size / $macro->serving_size) * $micro->saturates ?? 0, 1);
                        $fibre = round(($mealItem->serving_size / $macro->serving_size) * $micro->fibre ?? 0, 1);
                        $salt = round(($mealItem->serving_size / $macro->serving_size) * $micro->salt ?? 0, 1);
                    
                    }

                    $meal_calories += $calories;
                    $meal_fats += $fat ?? 0;
                    $meal_carbs += $carbs ?? 0;
                    $meal_protein += $protein ?? 0;

                    if(isset($micro)) {
                                            $meal_sugars += $sugars ?? 0;
                                            $meal_saturates += $saturates ?? 0;
                                            $meal_fibre += $fibre ?? 0;
                                            $meal_salt += $salt ?? 0;
                    }

                    
                    // dd($mealItem->food_id);

                    // dd(Food::where('id', $mealItem->food_id)->first()->name);
                    
                    $mealsData[$key]['items'][$key_meal] = [
                        'name' => Food::where('id', $mealItem->food_id)->first()->name,
                        'serving_size' => $macro->serving_size,
                        'serving_unit' => FoodUnit::where('id', $macro->food_unit_id)->first()->short_name,
                        'calories' => $calories,
                        'fat' => $fat,
                        'carbs' => $carbs,
                        'protein' => $protein,
                        'sugars' => $sugars ?? 0,
                        'saturates' => $saturates ?? 0,
                        'fibre' => $fibre ?? 0 ,
                        'salt' => $salt ?? 0,
                    ];
                }

                $mealsData[$key]['summary'] = [
                    // 'time_planned' => $meal->time_planned,
                    'calories' => $meal_calories,
                    'fat' => $meal_fats,
                    'carbs' => $meal_carbs,
                    'protein' => $meal_protein,
                    'sugars' => $meal_sugars ?? 0,
                    'saturates' => $meal_saturates ?? 0,
                    'fibre' => $meal_fibre ?? 0,
                    'salt' => $meal_salt ?? 0,
                ];

                $mealsData[$key]['meal'] = $meal; // Include original meal if needed
            }

        // dd($mealsData);

        // foreach($meals as $key => $value) {
            
        //     // dd($value);
            
        //     $meal_calories = 0;
        //     $meal_fats = 0;
        //     $meal_carbs = 0;
        //     $meal_protein = 0;

        //     $mealItems = MealItems::where('meal_id', $value->id)
        //                     ->get();

                            
        //     // $meal_calories = 0;

        //     foreach($mealItems as $key_meal => $mealItem) {

        //         $mealItem_macro = Macronutrients::where('food_id', $mealItem->food_id)
        //                                 ->first();

        //         // dd($mealItem_macro);

                
        //         $meal_calories += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->calories, 0);
        //         $meal_fats += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->fat, 1);
        //         $meal_carbs += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->carbohydrates, 1);
        //         $meal_protein += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->protein, 1);

        //         // dd($mealItem, $mealItem_macro);
                
        //         $meals[$key][$key_meal] = [];
        //         $meals[$key][$key_meal]['calories'] = round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->calories, 0);
        //         $meals[$key][$key_meal]['fat'] = round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->fat, 1);
        //         $meals[$key][$key_meal]['carbs'] = round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->carbohydrates, 1);
        //         $meals[$key][$key_meal]['protein'] = round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->protein, 1);


        //         $meals[$key]['calories'] = $meal_calories;
        //         $meals[$key]['fat'] = $meal_fats;
        //         $meals[$key]['carbs'] = $meal_carbs;
        //         $meals[$key]['protein'] = $meal_protein;

        //     }

        //     // dd($meals);





        // }

        // dd($meals);

                    // dd($meal_calories, $meal_fats, $meal_carbs, $meal_protein);
        
        // dd($meals);

        // dd($mealsData);

         
        //  dd($mealsData);

        $trendData = [];

        foreach ($mealsData as $dayIndex => $mealsOfDay) {
            // Initialize totals for this day
            $totals = [
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fat' => 0,
            ];

            foreach ($mealsOfDay as $meal) {
                foreach ($meal as $itemName => $nutrients) {
                    if ($itemName === 'name') continue; // skip the meal name entry

                    // Add values safely if keys exist
                    $totals['calories'] += $nutrients['calories'] ?? 0;
                    $totals['protein']  += $nutrients['protein'] ?? 0;
                    $totals['carbs']    += $nutrients['carbs'] ?? 0;
                    $totals['fat']      += $nutrients['fat'] ?? 0;
                }
            }

            $trendData[$dayIndex] = $totals;
        }

        //  foreach($mealsData as $key => $meal) {
        //     $trendData[$key] = [];

        //     // dd($meal);

        //     foreach($meal['items'] as $meal_item) {

        //         $trendData[$key][$meal_item['name']] = [
        //             'calories' => $meal_item['serving_size'],
        //             'serving_unit' => $meal_item['serving_unit'],
        //             'fat' => $meal_item['fat'],
        //             'carbs' => $meal_item['carbs'],
        //             'protein' => $meal_item['protein'],
        //             'sugars' => $meal_item['sugars'],
        //             'saturates' => $meal_item['saturates'],
        //             'fibre' => $meal_item['fibre'],
        //             'salt' => $meal_item['salt'],
        //         ];
        //     }

        //     $trendData[$key]['name'] = $meal['meal']->name;
        // }


        //  dd($trendData);

        //  $trendData = $mealsData->groupBy(fn($meal) => date('Y-m-d', strtotime($meal->time_planned)))
        //     ->map(function ($mealsOnDate) {
        //         $totals = [
        //             'calories' => 0,
        //             'protein' => 0,
        //             'carbs' => 0,
        //             'fat' => 0,
        //         ];
                
        //         // dd($mealsOnDate);

        //         foreach ($mealsOnDate as $meal) {
        //                 $macro = $meal;
        //                 if ($macro) {
        //                     // $multiplier = $item->quantity / $macro->serving_size;
        //                     $totals['calories'] += $macro->calories;
        //                     $totals['protein']  += $macro->protein; 
        //                     $totals['carbs']    += $macro->carbs; 
        //                     $totals['fat']      += $macro->fat;
        //                 }
        //             }

    

        //         return $totals;
        //     });

        // Format for Filament chart

        dd($trendData);
        
        $labels = $trendData->keys()->toArray();
        

        // set to dd/mm/yyyy format
        foreach ($labels as $key => $label) {
            $label = date('d M', strtotime($label));

            $labels[$key] = $label;
            
        }
        
        // dd($trendData);

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
        
        dd($popular_meal_items_display);

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
