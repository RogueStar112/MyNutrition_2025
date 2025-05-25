<?php

namespace App\Filament\Resources\MealResource\Pages;

use App\Filament\Resources\MealResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Carbon\Carbon;

use Auth;

use App\Models\Food;
use App\Models\Macronutrients;
use App\Models\Meal;
use App\Models\MealItems;
use App\Models\FoodUnit;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class CreateMeal extends CreateRecord
{
    protected static string $resource = MealResource::class;

    protected ?string $subheading = 'Create meals from existing food items. You can also save these meals as recipes for later!';

    protected function mutateFormDataBeforeCreate(array $data): array {

        // dd($data['food']);

        if (date('Y-m-d H:i:s', strtotime($data['time_planned'])) < Carbon::now()->format('Y-m-d H:i:s')) {
                                        $data['is_eaten'] = 1;
                                        $data['is_notified'] = 0;
                                    } else {
                                        $data['is_eaten'] = 0;
                                        $data['is_notified'] = 0;
                                    }

        // Clean up unnecessary fields
        unset($data['food_source']);

        return $data;


        }

    protected function afterCreate(): void
        {      

            $formData = $this->form->getState();
            
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->body('Changes to the post have been saved.')
                ->actions([
                    Action::make('view')
                        ->button()
                        ->markAsRead(),
                ])
                ->send();


            foreach($this->form->getState()['food'] as $food) {

                $user_id = Auth::user()->id;

                $newMeal_search = Meal::where('user_id', $user_id)
                            ->latest('id')
                            ->first();

                
                $meal_macros = Macronutrients::where('food_id', $food['food_id'])
                                            ->first();
                
                // dd($meal_macros);
                                
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

            
        }

}

