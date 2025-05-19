<?php

namespace App\Filament\Resources\MealResource\Pages;

use App\Filament\Resources\MealResource;
use Filament\Actions;

use Filament\Resources\Pages\ListRecords;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Placeholder;

use App\Models\MealItems;
use App\Models\Macronutrients;

use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;


class ListMeals extends ListRecords
{
    protected static string $resource = MealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('viewItems')
                ->label('View Items')
                ->icon('heroicon-o-eye')
                ->modalHeading(fn ($record) => "Items for {$record->title}")
                ->form(fn ($record) => [
                    Placeholder::make('meal_items')
                        ->label('Meal Items')
                        ->content(function () use ($record) {
                            return MealItems::where('meal_id', $record->id)
                                ->get()
                                ->pluck('name') // adjust field as needed
                                ->join(', ');
                        }),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
           ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('time_planned')
                ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                     Action::make('viewItems')
                    ->label(fn ($record) => 'View (' . $record->mealItems()->count() . ')')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => "Meal Items for: {$record->name}")
                    ->form(fn ($record) => [
                        Placeholder::make('meal_items')
                            ->label('Meal Items')
                            ->content(
                                MealItems::where('meal_id', $record->id)
                                    ->pluck('name')
                                    ->join(', ')
                            ),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            
    }

      protected function getHeaderWidgets(): array {
             return [
                MealResource\Widgets\DailyCaloriesChart::class,
                // MealResource\Widgets\MacroStats::class,
                MealResource\Widgets\StatsOverview::class,
             ];
     }


}
