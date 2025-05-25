<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\Food;
use App\Models\Meal;
use App\Models\MealItems;

use App\Models\RecipeItems;

use App\Models\FoodSource;
use App\Models\FoodUnit;
use App\Models\Macronutrients;

use App\Models\Tag;
use App\Models\RecipeTags;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;

use Illuminate\Support\HtmlString;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;

use Filament\Forms\Get;

use Filament\Actions\Action;

use Filament\Forms\Components\Select;

use Auth;

use Carbon\Carbon;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

     protected function getHeaderActions(): array {
        return [
            Action::make('importMeal')
                    ->label('Import Meal')
                    ->color('success')
                    ->icon('heroicon-m-sparkles')
                    ->form([
                        Select::make('import_meal_to_recipe')
                            ->label('Select Meal to Import')
                            ->options(Meal::where('user_id', Auth::id())->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): array {
                        $formData = $this->form->getState();

                        // dd($formData);
                        // dd($data);

                        $mealItems = MealItems::where('meal_id', $data['import_meal_to_recipe'])->get();

                        foreach($mealItems as $key => $value) {

                            $macros = Macronutrients::where('food_id', $value['food_id'])
                                            ->first();

                            $data['food'][$key] = $value; 
                            
                            // dd(Food::where('id', $value['food_id'])->first()->source_id);

                            $data['food'][$key]['source_name'] = FoodSource::find(Food::where('id', $value['food_id'])->first()->source_id)->name;


                            $data['food'][$key]['serving_unit_label'] = FoodUnit::find($value['food_unit_id'])->name;
                            $data['food'][$key]['calories'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['calories'], 0);
                            $data['food'][$key]['fat'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['fat'], 0);
                            $data['food'][$key]['carbs'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['carbohydrates'], 0);
                            $data['food'][$key]['protein'] = round(($value['serving_size'] / $macros['serving_size']) * $macros['protein'], 0);

                        }

                        // dd($formData);

                        // dd($data);

                        $this->form->fill([
                            'name' => $formData['name'],
                            'subheading' => $formData['subheading'],
                            'tags' => $formData['tags'],
                            'food' => $data['food'],
                            'user_id' => $formData['user_id']
                        ]);

                        return $data;
                    })
        ];

    }

    protected function afterCreate(): void
    {
        $formData = $this->form->getState();

        // dd($formData);

         foreach($formData['tags'] as $index => $tag_name) {

            $tag_record = Tag::firstOrCreate([
                'name' => $tag_name,
                'color_text' => "#FFA500",
                'color_bg' => "#000000",
                'user_id' => Auth::id(),
            ]);

            RecipeTags::create([
                'recipe_id' => $this->record->id,
                'tag_id' => $tag_record->id,
            ]);

         }

         foreach($formData['food'] as $food) {              
            $meal_macros = Macronutrients::where('food_id', $food['food_id'])
                                        ->first();
                                        

            // $food_name = Food::find($food['food_id'])->name;

            RecipeItems::create([
                
                'recipe_id' => $this->record->id,
                'food_id' => $food['food_id'],
                'food_unit_id' => $food['food_unit_id'],
                'serving_size' => $food['serving_size'],
                'quantity' => 1,
                'description' => '',
     

            ]);

        }
    }
}
