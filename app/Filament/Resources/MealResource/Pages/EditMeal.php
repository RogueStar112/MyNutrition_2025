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
                $data['food'][$key]['calories'] = $macros['calories'];
                $data['food'][$key]['fat'] = $macros['fat'];
                $data['food'][$key]['carbs'] = $macros['carbohydrates'];
                $data['food'][$key]['protein'] = $macros['protein'];

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

                       $mealItems = MealItems::where('meal_id', $data['id'])
                                        ->get(); 
            }


}
