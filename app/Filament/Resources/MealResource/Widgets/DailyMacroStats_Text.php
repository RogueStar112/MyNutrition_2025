<?php

namespace App\Filament\Resources\MealResource\Widgets;


use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

use App\Models\Food;
use App\Models\Macronutrients;
use App\Models\Micronutrients;
use App\Models\Meal;
use App\Models\MealItems;
use App\Models\FoodUnit;

class DailyMacroStats_Text extends BaseWidget
{
    protected static string $view = 'filament.widgets.daily-macro-stats-text';

    protected int | string | array $columnSpan = '1';

    public function getMacroData(): array {

    }

    public function getViewData(): array
    {
        return [
            'userCount' => User::count(),
            'latestUsers' => User::latest()->take(5)->get(),
        ];
    }
}