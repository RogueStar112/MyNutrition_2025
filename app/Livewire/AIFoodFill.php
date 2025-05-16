<?php

namespace App\Livewire;

use Livewire\Component;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;


class AIFoodFill extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function foodPrompt(Request $request, $name, $serving_size, $source, $serving_unit): Action {

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                // can use 'system' in role alternatively.
                ['role' => 'user', 'content' => "Responding with pure JSON, can you provide the nutritional content for $name (per $serving_size $serving_unit, from $source), in addition the data source? Return the following ONLY: Calories (kcal), Fat (g), Carbs (g), Protein (g), Sugars (g), Saturates (g), Fibre (g), Salt (g), dataSource. "],
            ],
        ]);

        return response()->json(['result' => $result->choices[0]->message->content]);
    }
}
