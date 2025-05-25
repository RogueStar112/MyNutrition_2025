<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use App\Models\Food;
use App\Models\FoodUnit;
use App\Models\FoodSource;
use App\Models\Meal;
use App\Models\User;
use App\Models\Macronutrients;
use App\Models\MealItems;
use App\Models\RecipeItems;

use App\Models\Tag;
use App\Models\RecipeTags;


class EditRecipe extends EditRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
        {

        
            $recipeTags = RecipeTags::where('recipe_id', $this->record->id)
                                        ->get();

            foreach($recipeTags as $index => $tag) {
                $data['tags'][$index] = Tag::find($tag->tag_id)->name;
            }

            // dd($data);

              $mealItems = RecipeItems::where('recipe_id', $this->record->id)
                                        ->get();

            // dd($mealItems);

           

            foreach($mealItems as $key => $value) {

                // dd($value);

                $macros = Macronutrients::where('food_id', $value['food_id'])
                                            ->first();

                $data['food'][$key] = $value; 

                // dd(Food::where('id', $value['food_id'])->first()->source_id);
                $data['food'][$key]['name'] = Food::find($value['food_id'])->name; 
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


}
