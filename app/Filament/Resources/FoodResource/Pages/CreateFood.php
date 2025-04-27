<?php

namespace App\Filament\Resources\FoodResource\Pages;

use App\Filament\Resources\FoodResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\Food;
use App\Models\FoodSource;
use App\Models\Macronutrients;
use App\Models\Micronutrients;

use Illuminate\Database\Eloquent\Model;

use Auth;

class CreateFood extends CreateRecord
{

    
    protected static string $resource = FoodResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array {

        
          // Check if FoodSource exists, otherwise create
        $foodSource = FoodSource::firstOrCreate(
            [
                'name' => $data['food_source'],
                'user_id' => Auth::id(),
            ],
            [
                'name' => $data['food_source'],
                'user_id' => Auth::id(),
            ]
        );

        // Set source_id for the Food
        $data['source_id'] = $foodSource->id;

        // Clean up unnecessary fields
        unset($data['food_source']);

        return $data;


        }

        protected function afterCreate(): void
        {
            Macronutrients::create([
                'food_id' => $this->record->id,
                'food_unit_id' => $this->form->getState()['food_unit'],
                'serving_size' => $this->form->getState()['serving_size'],
                'calories' => $this->form->getState()['calories'],
                'fat' => $this->form->getState()['fat'],
                'carbohydrates' => $this->form->getState()['carbohydrates'],
                'protein' => $this->form->getState()['protein'],
            ]);
        }

        // protected function handleRecordCreation(array $data): Model
        // {
        //     return static::getModel();
        // }

}
