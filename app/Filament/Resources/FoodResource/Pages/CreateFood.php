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

use Filament\Actions\Action;
use Filament\Forms\Get;


use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

use Filament\Pages\Actions\CreateAction;

use Auth;

use Illuminate\Http\JsonResponse;
use Filament\Notifications\Notification;

class CreateFood extends CreateRecord
{

    
    protected static string $resource = FoodResource::class;

    protected ?string $subheading = 'Create new food items. AI Auto Fill requires Step 1, 2, and 3, which fills in macros for Step 4 & 5.';

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

            Micronutrients::create([
                'food_id' => $this->record->id,
                'sugars' => $this->form->getState()['sugars'],
                'saturates' => $this->form->getState()['saturates'],
                'fibre' => $this->form->getState()['fibre'],
                'salt' => $this->form->getState()['salt'],
            ]);
        }


        protected function foodPrompt($name, $serving_size, $source, $serving_unit): JsonResponse {

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    // can use 'system' in role alternatively.
                    ['role' => 'user', 'content' => "Responding with pure JSON, can you provide the nutritional content for $name (per $serving_size $serving_unit), from $source, in addition to its data source? Return the following ONLY: Calories (kcal), Fat (g), Carbs (g), Protein (g), Sugars (g), Saturates (g), Fibre (g), Salt (g), DataSource. "],
                ],
            ]);

            return response()->json(['result' => $result->choices[0]->message->content]);
        }

        public static function canCreateAnother(): bool
        {
            return true;
        }

    
}
