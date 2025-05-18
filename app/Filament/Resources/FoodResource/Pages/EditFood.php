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
use App\Models\Micronutrients;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

use Filament\Actions\Action;


use Filament\Pages\Actions\CreateAction;

use Illuminate\Http\JsonResponse;
use Filament\Notifications\Notification;

class EditFood extends EditRecord
{
    protected static string $resource = FoodResource::class;

      protected ?string $subheading = 'Edit a food item. AI Auto Fill requires Step 1, 2, and 4, which fills in macros for Step 3.';

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

    protected function getHeaderActions(): array
    {
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

                        // dd($foodPrompt_result);

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

                        Actions\DeleteAction::make()
                               ->icon('heroicon-m-trash'),
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

            $micronutrient_data = Micronutrients::where('food_id', $data['id'])
                                 ->first();

            // dd($macronutrient_data->calories);

                        
            $data['food_unit'] = $macronutrient_data->food_unit_id ?? 1;
            $data['serving_size'] = $macronutrient_data->serving_size ?? 0;
            
            $data['calories'] = $macronutrient_data->calories ?? 0;
            $data['fat'] = $macronutrient_data->fat ?? 0;
            $data['carbohydrates'] = $macronutrient_data->carbohydrates?? 0;
            $data['protein'] = $macronutrient_data->protein ?? 0;

            $data['sugars'] = $micronutrient_data->sugars ?? 0;
            $data['saturates'] = $micronutrient_data->saturates ?? 0;
            $data['fibre'] = $micronutrient_data->fibre ?? 0 ;
            $data['salt'] = $micronutrient_data->salt ?? 0;

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

            


            $micronutrient_data = Micronutrients::updateOrCreate(['food_id' => $record['id']], ['sugars' => $data['sugars'], 'saturates' => $data['saturates'], 'fibre' => $data['fibre'], 'salt' => $data['salt']]);
                                    
            // $micronutrient_data->sugars = $data['sugars'] ?? 0;
            // $micronutrient_data->saturates = $data['saturates'] ?? 0;
            // $micronutrient_data->fibre = $data['fibre'] ?? 0 ;
            // $micronutrient_data->salt = $data['salt'] ?? 0;

            $record->update($data);

            return $record;
        }

}
