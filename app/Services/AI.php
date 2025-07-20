<?php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class AI {
  static public function Generate(string $name, string $serving_size, string $serving_unit, string $source, /* ...*/) {
             $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    // can use 'system' in role alternatively.
                    ['role' => 'user', 'content' => "Responding with pure JSON, can you provide the nutritional content for $name (per $serving_size $serving_unit), from $source, in addition to its data source? Return the following ONLY: Calories (kcal), Fat (g), Carbs (g), Protein (g), Sugars (g), Saturates (g), Fibre (g), Salt (g), DataSource. "],
                ],
            ]);

            return response()->json(['result' => $result->choices[0]->message->content]);
  }

    static public function ImageGenerate(string $image_url) {
             $result = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => 'could you return two things from this image: 1. a description of the foods ("description") 2. the nutritional content from them ("nutrients" -> "calories", "fat", "carbs", "protein") all wrapped in one JSON structure?'],
                                // Replace with a real, accessible image
                                ['type' => 'image_url', 'image_url' => ['url' => "$image_url"]], 
                            ]
                        ]
                    ]

                            ]);

            return response()->json(['result' => $result->choices[0]->message->content]);
  }
}

/*
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => 'What is in this image?'],
                // Replace with a real, accessible image
                ['type' => 'image_url', 'image_url' => ['url' => 'https://example.com/image.jpg']], 
            ]
        ]
    ]
*/