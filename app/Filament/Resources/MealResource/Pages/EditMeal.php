<?php

namespace App\Filament\Resources\MealResource\Pages;

use App\Filament\Resources\MealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use App\Models\Food;
use App\Models\FoodUnit;
use App\Models\FoodSource;
use App\Models\Meal;
use App\Models\User;
use App\Models\Macronutrients;
use App\Models\MealItems;

use Illuminate\Database\Eloquent\Model;

use Auth;

class EditMeal extends EditRecord
{
    protected static string $resource = MealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
        {

            // dd($data);

            $mealItems = MealItems::where('meal_id', $data['id'])
                                        ->get();

            // dd($mealItems);


            foreach($mealItems as $key => $value) {

                
                $macros = Macronutrients::where('food_id', $value['food_id'])
                                            ->first();

                $data['food'][$key] = $value; 
                
                // dd(Food::where('id', $value['food_id'])->first()->source_id);

                $data['food'][$key]['source_name'] = FoodSource::find(Food::where('id', $value['food_id'])->first()->source_id)->name;


                $data['food'][$key]['serving_unit_label'] = FoodUnit::find($value['food_unit_id'])->name;
                $data['food'][$key]['calories'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['calories'], 0);
                $data['food'][$key]['fat'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['fat'], 0);;
                $data['food'][$key]['carbs'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['carbohydrates'], 0);;
                $data['food'][$key]['protein'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['protein'], 0);;

            }

            // dd($data);

            // $foodsource_data = FoodSource::where('id', $data['source_id'])
            //                     ->first();

            
            // $data['food_source'] = $foodsource_data->name;

 


            // $macronutrient_data = Macronutrients::where('food_id', $data['id'])
            //                         ->first();

            // // dd($macronutrient_data->calories);

                        
            // $data['food_unit'] = $macronutrient_data->food_unit_id;
            // $data['serving_size'] = $macronutrient_data->serving_size;
            // $data['calories'] = $macronutrient_data->calories;
            // $data['fat'] = $macronutrient_data->fat;
            // $data['carbohydrates'] = $macronutrient_data->carbohydrates;
            // $data['protein'] = $macronutrient_data->protein;


            return $data;


        }

    protected function handleRecordUpdate(Model $record, array $data): Model


            {   

                    //    dd($record, $data);

                       $mealSelected = Meal::find($record['id']);
                       
                       $mealSelected->name = $data['name'];
                       $mealSelected->time_planned = $data['time_planned'];

                       $mealSelected->update();
                       $mealSelected->touch();


                       $mealItems_selected_delete = MealItems::where('meal_id', $data['food'][0]['meal_id'])
                                                ->delete();

                    //    $mealItems_selected->delete();


                        // $mealItems_selected->update();

                    //    dd($mealItems_selected);

                    //    $record->update($data);

                    $user_id = Auth::user()->id;
                
                    $newMeal_search = Meal::where('user_id', $user_id)
                                ->latest('id')
                                ->first();


                    foreach($data['food'] as $food) {
                        


                        
                        $meal_macros = Macronutrients::where('food_id', $food['food_id'])
                                                    ->first();
                                                    

                        $food_name = Food::find($food['food_id'])->name;

                        MealItems::create([
                            'name' => $food_name,
                            'meal_id' => $newMeal_search->id,
                            'food_id' => $food['food_id'],
                            'food_unit_id' => $meal_macros['food_unit_id'],
                            'serving_size' => $food['serving_size'],
                            'quantity' => 1,
                            'calories' => round(($meal_macros['serving_size'] / $food['serving_size']) * $meal_macros['calories'], 0),
                            'fat' =>  round(($meal_macros['serving_size'] / $food['serving_size']) * $meal_macros['fat'], 0),
                            'carbohydrates' =>  round(($meal_macros['serving_size'] / $food['serving_size']) * $meal_macros['carbohydrates'], 0),
                            'protein' =>  round(($meal_macros['serving_size'] / $food['serving_size']) * $meal_macros['protein'], 0),
                        ]);
                    }
                        
                       return $record;



            }


}
