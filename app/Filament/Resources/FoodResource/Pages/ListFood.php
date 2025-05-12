<?php

namespace App\Filament\Resources\FoodResource\Pages;

use App\Filament\Resources\FoodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Food;


class ListFood extends ListRecords
{
    protected static string $resource = FoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // protected function getTableQuery()
    // {   
    //     return Food::query()->latest(); // same as ->orderBy('created_at', 'desc')
    // }

}
