<?php

namespace App\Filament\Resources\FoodResource\Pages;

use App\Filament\Resources\FoodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Auth;

use App\Models\Food;
use App\Models\FoodSource;
use App\Models\Meal;
use App\Models\User;
use App\Models\Macronutrients;

use Illuminate\Database\Eloquent\Model;

class EditFood extends EditRecord
{
    protected static string $resource = FoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
        {

            // dd($data);
            $foodsource_data = FoodSource::where('id', $data['source_id'])
                                ->first();

            
            $data['food_source'] = $foodsource_data->name;

 


            $macronutrient_data = Macronutrients::where('food_id', $data['id'])
                                    ->first();

            // dd($macronutrient_data->calories);

                        
            $data['food_unit'] = $macronutrient_data->food_unit_id;
            $data['serving_size'] = $macronutrient_data->serving_size;
            $data['calories'] = $macronutrient_data->calories;
            $data['fat'] = $macronutrient_data->fat;
            $data['carbohydrates'] = $macronutrient_data->carbohydrates;
            $data['protein'] = $macronutrient_data->protein;


            return $data;
        }

        protected function handleRecordUpdate(Model $record, array $data): Model


        {   
         

            // $foodsource_data = FoodSource::where('id', $record['source_id'])
            //                     ->first();

            $foodsource_data = FoodSource::firstOrCreate(
                [
                    'name' => $data['food_source'],
                    'user_id' => Auth::id(),
                ],
                [
                    'name' => $data['food_source'],
                    'user_id' => Auth::id(),
                ]
            );

            // dd($foodsource_data);

            

            $food_to_update_source = Food::where('source_id', $record['source_id'])
                                              ->where('user_id', Auth::id())
                                              ->where('id', $record['id'])
                                              ->first();

            $food_to_update_source->source_id = $foodsource_data->id;

            $data['source_id'] = $foodsource_data->id;

            $food_to_update_source->save();



            $macronutrient_data = Macronutrients::where('food_id', $record['id'])
                                    ->first();

            $macronutrient_data->food_unit_id = $data['food_unit'];
            $macronutrient_data->serving_size = $data['serving_size'];
            $macronutrient_data->calories = $data['calories'];
            $macronutrient_data->fat = $data['fat'];
            $macronutrient_data->carbohydrates = $data['carbohydrates'];
            $macronutrient_data->protein = $data['protein'];

            $macronutrient_data->save();

            $record->update($data);

            return $record;
        }

}
