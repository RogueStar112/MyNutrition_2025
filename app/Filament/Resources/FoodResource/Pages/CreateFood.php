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

        protected function getHeaderActions(): array {
        return [
                Action::make('aiAutoFill')
                    ->label('AI Auto Fill')
                    ->color('success')
                    ->icon('heroicon-m-sparkles')
                    ->action(function () {
                        // Access unsubmitted form data
                        $formData = $this->form->getState();

                        $name = $formData['name'] ?? null;
                        $source = $formData['food_source'] ?? null;
                        $foodUnit = $formData['food_unit'] ?? null;
                        $servingSize = $formData['serving_size'] ?? null;

                        $foodPrompt = $this->foodPrompt($formData['name'], $formData['serving_size'], $formData['food_unit'], $formData['food_source']);

                        
                  

                        $foodPrompt_JSON = $foodPrompt->getContent();
                        $foodPrompt_array = json_decode($foodPrompt_JSON, true);
                    
                        $foodPrompt_result = json_decode($foodPrompt_array['result'], true);

                        // ✨ Use AI logic or API call here (stubbed for now)

                        $calculatedCalories = $foodPrompt_result['Calories'] ?? $foodPrompt_result['Calories (kcal)'] ?? NULL;
                        $calculatedFats = $foodPrompt_result['Fat']  ?? $foodPrompt_result['Fat (g)'] ?? NULL;
                        $calculatedCarbs = $foodPrompt_result['Carbs'] ?? $foodPrompt_result['Carbs (g)'] ?? NULL;
                        $calculatedProtein = $foodPrompt_result['Protein'] ?? $foodPrompt_result['Protein (g)'] ?? NULL;

                        $calculatedSugars = $foodPrompt_result['Sugars'] ?? $foodPrompt_result['Sugars (g)'] ?? NULL;
                        $calculatedSaturates = $foodPrompt_result['Saturates'] ?? $foodPrompt_result['Saturates (g)'] ?? NULL;
                        $calculatedFibre = $foodPrompt_result['Fibre'] ?? $foodPrompt_result['Fibre (g)'] ?? NULL;
                        $calculatedSalt = $foodPrompt_result['Salt'] ?? $foodPrompt_result['Salt (g)'] ?? NULL;
                        
                        $sourceData = $foodPrompt_result['DataSource'];

                        // Set form state (e.g., to auto-fill a calories field)
                        $this->form->fill([
                            'name' => $formData['name'],
                            'food_source' => $formData['food_source'],
                            'serving_size' => $formData['serving_size'],
                            'food_unit' => $formData['food_unit'],

                            'calories' => $calculatedCalories,
                            'fat' => $calculatedFats,
                            'carbohydrates' => $calculatedCarbs,
                            'protein' => $calculatedProtein,

                            'sugars' => $calculatedSugars,
                            'saturates' => $calculatedSaturates,
                            'fibre' => $calculatedFibre,
                            'salt' => $calculatedSalt,

                            'user_id' => $formData['user_id'],
                            'description' => "🔎 AI. Source: $sourceData" 
                        ]);

                        Notification::make()
                            ->title('Macros estimated and filled in!')
                            ->success()
                            ->send();
                    }),
                ];
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

        // protected function handleRecordCreation(array $data): Model
        // {
        //     return static::getModel();
        // }

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

        // protected function getFormActions(): array
        // {   
        //     return [
        //         // CreateAction::make()
        //         //     ->model(Food::class),

                // Action::make('aiAutoFill')
                //     ->label('AI Auto Fill')
                //     ->action(function () {
                //         // Access unsubmitted form data
                //         $formData = $this->form->getState();

                //         $name = $formData['name'] ?? null;
                //         $source = $formData['food_source'] ?? null;
                //         $foodUnit = $formData['food_unit'] ?? null;
                //         $servingSize = $formData['serving_size'] ?? null;

                //         $foodPrompt = $this->foodPrompt($formData['name'], $formData['serving_size'], $formData['food_unit'], $formData['food_source']);

                //         $foodPrompt_JSON = $foodPrompt->getContent();
                //         $foodPrompt_array = json_decode($foodPrompt_JSON, true);
                    
                //         $foodPrompt_result = json_decode($foodPrompt_array['result'], true);

                //         // ✨ Use AI logic or API call here (stubbed for now)



                //         $calculatedCalories = $foodPrompt_result['Calories (kcal)'];
                //         $calculatedFats = $foodPrompt_result['Fat (g)'];
                //         $calculatedCarbs = $foodPrompt_result['Carbs (g)'];
                //         $calculatedProtein = $foodPrompt_result['Protein (g)'];


                //         // Set form state (e.g., to auto-fill a calories field)
                //         $this->form->fill([
                //             'name' => $formData['name'],
                //             'food_source' => $formData['food_source'],
                //             'serving_size' => $formData['serving_size'],
                //             'food_unit' => $formData['food_unit'],

                //             'calories' => $calculatedCalories,
                //             'fat' => $calculatedFats,
                //             'carbohydrates' => $calculatedCarbs,
                //             'protein' => $calculatedProtein
                //         ]);

                //         Notification::make()
                //             ->title('Macros estimated and filled in!')
                //             ->success()
                //             ->send();
                //     }),

        //             CreateAction::make(), 
        //     ];
        // }
}
