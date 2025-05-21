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

use Illuminate\Support\HtmlString;


class StatsOverview extends BaseWidget
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

        $avg_calories_mspd = array_slice($trendData->pluck('calories')->map(fn($val) => round($val))->toArray(), -7, 7, true);

        // dd($avg_calories_mspd);

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

        // $popular_meal_items_display = implode(", <br>", $popular_meal_items->pluck('name')->map(fn($val) => $val)->toArray());
        
        $popular_meal_items_array_name = [];
        $popular_meal_items_array_points = [];

        foreach($popular_meal_items as $popular_meal_item) {
            array_push($popular_meal_items_array_name, $popular_meal_item->name);
            array_push($popular_meal_items_array_points, $popular_meal_item->total);
        }

        // dd($popular_meal_items_array);

        // dd($popular_meal_items_display);

        $last2records = [];

        $trendData_last2records = array_slice($trendData->toArray(), -2, 2, true);
        
        foreach($trendData_last2records as $key => $record) {
            array_push($last2records, ['date' => $key, 'macros' => $record]);
            // $last2records[$key]['date'] = $key;
        }

        
        // $second_latest_calories = $last2records[0]['macros']['calories'];

        $latest_macros = $last2records[1]['macros'];



        $latest_macros_date = $last2records[1]['date'];
        $latest_calories = $last2records[1]['macros']['calories'];
        $second_latest_calories = $last2records[0]['macros']['calories'];

        // dd($latest_calories, $second_latest_calories);
        $calorie_difference_perc = round((($latest_calories - $second_latest_calories) / (($latest_calories + $second_latest_calories) / 2)) * 100, 1);

        

        return [
        Stat::make("Latest Calories - " . date('d M', strtotime($latest_macros_date)), "$latest_calories" . "kcal")
            ->description("$calorie_difference_perc%" . ($calorie_difference_perc > 0 ? " more " : " less ") . " compared to " . date('d M', strtotime($last2records[0]['date'])) . " (" . $last2records[0]['macros']['calories'] . "kcal)")
            ->descriptionIcon(($calorie_difference_perc > 0) ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down' )
            // ->chart($trendData->pluck('calories')->map(fn($val) => round($val))->toArray())
            ->chart([$second_latest_calories, $latest_calories])
            ->color(($calorie_difference_perc > 0) ? 'danger' : 'success'),
        Stat::make('Average Calories Per Day', "$avg_calories_mspd" . "kcal")
            ->description("From $last7days to $today")
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart($trendData->pluck('calories')->map(fn($val) => round($val))->toArray())
            ->color('success'),
        Stat::make('Top 3 Popular Meal Items this week', "")
            ->description("From $last7days to $today")
            ->label(new HtmlString("
            <div class='flex flex-col w-full gap-1' style='width: 21.313rem;'>
            <h2 style='font-size: 0.875rem;'>Top 3 Items</h2><br>
            <p style='background: #EDDD53;
background: linear-gradient(90deg,rgba(237, 221, 83, 1) 5%, rgba(24, 24, 27, 1) 100%); color: black; padding: 0.325rem; width: 100%; border-top-left-radius: 5%; border-bottom-left-radius: 5%;'>1. $popular_meal_items_array_name[0]: $popular_meal_items_array_points[0]x</p>
            <p style='background: #bfbdac;
background: linear-gradient(90deg,rgba(191, 189, 172, 1) 5%, rgba(24, 24, 27, 1) 100%); color: black; padding: 0.325rem; width: 80%; border-top-left-radius: 5%; border-bottom-left-radius: 5%;'>2. $popular_meal_items_array_name[1]: $popular_meal_items_array_points[1]x</p>
            <p style='background: #47450e;
background: linear-gradient(90deg,rgba(71, 69, 14, 1) 5%, rgba(24, 24, 27, 1) 100%); color: lightgray; padding: 0.325rem; width: 50%; border-top-left-radius: 5%; border-bottom-left-radius: 5%;'>3. $popular_meal_items_array_name[2]: $popular_meal_items_array_points[2]x</p>
            </div>

            "))
            ->extraAttributes([
                'class' => 'w-full',
                'style' => 'min-width: 300px;',
            ])
            ->color('info'),
        ];
    }
}
