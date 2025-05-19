<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AIFoodFill;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/nutrition/ai/food_prompt/{name}/{serving_size}/{source}/{serving_unit}', [OpenAIController::class, 'food_prompt'])->name('ai.food_prompt');

