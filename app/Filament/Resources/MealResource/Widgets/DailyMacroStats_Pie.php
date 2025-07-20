<?php

namespace App\Filament\Resources\MealResource\Widgets;

use Filament\Widgets\ChartWidget;

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

class DailyMacroStats_Pie extends ChartWidget
{
    // protected ?string $heading = 'Macros';

    protected int | string | array $columnSpan = '1';

    protected static ?string $maxHeight = '400px';
    
    public ?string $filter = 'today';

    protected $listeners = ['getType' => '$refresh'];

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today/Most recent',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
            'all-time' => 'Lifetime'
        ];
    }

    public function getHeading(): string {
        $activeFilter = $this->filter;

        return "Macros - $activeFilter";
    }

    protected function getData(): array
    {   
        $activeFilter = $this->filter;

        $this->getType();

        $meals = Meal::with('mealItems')
            ->whereBetween('time_planned', [now()->subDays(30), now()])
            ->get()
            ->sortBy('time_planned');

        // dd($meals->pluck('name'));

        // analysis to check for mealItems
        
        // $mealItems = MealItems::all()->pluck('created_at');

        // dd($mealItems);

        // $dates = MealItems::all()
        //     ->pluck('created_at')
        //     ->map(function ($date) {
        //         return $date->diffForHumans(); // or ->addDays(5), etc.
        //     });

        // dd($dates);

        foreach($meals as $key => $value) {
            
            // dd($value);
            
            $meal_calories = 0;
            $meal_fats = 0;
            $meal_carbs = 0;
            $meal_protein = 0;
            
            $meal_sugars = 0;
            $meal_saturates = 0;
            $meal_fibre = 0;
            $meal_salt = 0;

            $mealItems = MealItems::where('meal_id', $value->id)
                            ->get();

            
            // $meal_calories = 0;

            foreach($mealItems as $mealItem) {

                $mealItem_macro = Macronutrients::where('food_id', $mealItem->food_id)
                                        ->first();

                $mealItem_micro = Micronutrients::where('food_id', $mealItem->food_id)
                                        ->first();

                // dd($mealItem_macro);

                $meal_calories += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->calories, 0);
                $meal_fats += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->fat, 1);
                $meal_carbs += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->carbohydrates, 1);
                $meal_protein += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_macro->protein, 1);

                $meal_sugars += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_micro?->sugars ?? 0, 0);
                $meal_saturates += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_micro?->saturates ?? 0, 1);
                $meal_fibre += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_micro?->fibre ?? 0, 1);
                $meal_salt += round(($mealItem->serving_size / $mealItem_macro->serving_size) * $mealItem_micro?->salt ?? 0, 2);

                $meals[$key]['calories'] = $meal_calories;
                $meals[$key]['fat'] = $meal_fats;
                $meals[$key]['carbs'] = $meal_carbs;
                $meals[$key]['protein'] = $meal_protein;

                $meals[$key]['sugars'] = $meal_sugars;
                $meals[$key]['saturates'] = $meal_saturates;
                $meals[$key]['fibre'] = $meal_fibre;
                $meals[$key]['salt'] = $meal_salt;

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
                    'sugars' => 0,
                    'saturates' => 0,
                    'fibre' => 0,
                    'salt' => 0
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

                            
                            $totals['sugars'] += $macro->sugars;
                            $totals['saturates'] += $macro->saturates;
                            $totals['fibre'] += $macro->fibre;
                            $totals['salt'] += $macro->salt;
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

        if ($activeFilter === 'today') {
            $labels = [0 => end($labels)];
        }

        // dd($labels);

        // if ($activeFilter === 'today') {
        //     $trendData = end($trendData);
        //     dd($trendData);
        // }
        
        
        // dd($trendData, gettype($trendData), end(array_values((array)$trendData)[0]));

        if ($activeFilter === 'today') {
            // dd($labels);

            $trendData = [$labels[0] => end(array_values((array)$trendData)[0])];

            // dd($trendData);
            // dd($trendData, $labels);

            // dd($trendData);

            return [
            'datasets' => [

                // [
                //     'label' => 'Calories',
                //     'data' => [$trendData[$labels[0]]['calories']],
                //     'borderColor' => '#f87171', // Red
                // ],

                [
                    // 'label' => '05 July',
                   'data' => [
                        $trendData[$labels[0]]['fat'] ?? 0,
                        $trendData[$labels[0]]['carbs'] ?? 0,
                        $trendData[$labels[0]]['protein'] ?? 0,
                    ],
                    'borderColor' => ['white'], // Yellow
                    'backgroundColor' => ['orange', 'blue', 'green'], // Yellow
                    'hidden' => false,
                    'hoverOffset' => 4
                ],

                
                // [
                //     'label' => 'Carbs',
                //     'data' => [$trendData[$labels[0]]['carbs']],
                //     'borderColor' => '#60a5fa', // Blue
                //     'hidden' => true,
                // ],
                
                // [
                //     'label' => 'Protein',
                //     'data' => [$trendData[$labels[0]]['protein']],
                //     'borderColor' => '#34d399', // Green
                //     'hidden' => true,
                // ],

                // [
                //     'label' => 'Sugars',
                //     'data' => [$trendData[$labels[0]]['sugars']],
                //     'borderColor' => '#ffa500', // Orange
                //     'hidden' => true,
                // ],

                // [
                //     'label' => 'Salt',
                //     'data' => [$trendData[$labels[0]]['salt']],
                //     'borderColor' => '#ffffff', // White
                //     'hidden' => true,
                // ],
            ],
                'labels' => ['Fat', 'Carbs', 'Protein'],
            ];
        } else {
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
                        'hidden' => true,
                    ],

                    
                    [
                        'label' => 'Carbs',
                        'data' => $trendData->pluck('carbs')->map(fn($val) => round($val))->toArray(),
                        'borderColor' => '#60a5fa', // Blue
                        'hidden' => true,
                    ],
                    
                    [
                        'label' => 'Protein',
                        'data' => $trendData->pluck('protein')->map(fn($val) => round($val))->toArray(),
                        'borderColor' => '#34d399', // Green
                        'hidden' => true,
                    ],

                    [
                        'label' => 'Sugars',
                        'data' => $trendData->pluck('sugars')->map(fn($val) => round($val))->toArray(),
                        'borderColor' => '#ffa500', // Orange
                        'hidden' => true,
                    ],

                    [
                        'label' => 'Salt',
                        'data' => $trendData->pluck('salt')->map(fn($val) => round($val))->toArray(),
                        'borderColor' => '#ffffff', // White
                        'hidden' => true,
                    ],
            ],
            'labels' => $labels,
        ];
        }
        
        // dd($trendData->keys()[end($trendData->keys())]);
        // dd(array_values(array_slice($trendData, -1))[0]);
        
        $this->getType();
        
       
    }

    protected function getType(): string
    {
        
        return 'doughnut';
 
    }


}
