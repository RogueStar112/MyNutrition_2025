<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;


use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\FoodSource;
use App\Models\Macronutrients;
use App\Models\Micronutrients;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ComponentAction;

use Filament\Forms\Get;
use Filament\Forms\Set;

use App\Services\AI;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;

use Illuminate\Support\HtmlString;

use Filament\Notifications\Notification;

use Filament\Forms\Components\FileUpload;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use LevelUp\Experience\Contracts\Multiplier;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $description = '✨ inputs are required for AI Auto Fill.';

    protected ?string $subheading = 'This is the subheading.';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Fieldset::make('Step 1. Name and Source')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->helperText('The name of the food you want to add.')
                    ->placeholder('Ricotta Cheese')
                    ->required()
                    ->maxLength(64),


                    Forms\Components\TextInput::make('food_source')
                    ->label('Food source')
                    ->helperText('Where the food came from.')
                    ->placeholder('Aldi')
                    ->required()
                    ->maxLength(64),
                ]),
                
        
                Fieldset::make('Step 2. Serving size and units')
                    ->schema([
                           Forms\Components\TextInput::make('serving_size')
                            ->label('Serving size')
                            ->placeholder('100')
                            ->required(),

                            // food units are arbitrarily preloaded in the database, and is currently simplified.

                            Forms\Components\Select::make('food_unit')
                            ->label('Food unit')
                            ->options([
                                '1' => 'grams (g)',
                                '5' => 'pieces (pcs)',
                                '6' => 'portions (x)',
                                '7' => 'slice (slices)',
                                '8' => 'tablespoons (tbsp)',
                                '9' => 'teaspoons (tsp)',
                                '10' => 'mililitres (ml)',
                                '12' => 'litres (l)'
                            ])
                            ->required(),
                            ]),
             

                            // Macronutrient fields
                
                    
                Fieldset::make('Step 3. User and Description')
                 ->schema([
                    Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->required()
                            ->password()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                    ])
                    ->required(),

                    Forms\Components\TextInput::make('description')
                    ->maxLength(256),
                ]),

               Actions::make([
                     ComponentAction::make('aiAutoFill')
                     ->label('✨ AI Auto Fill')
                     ->color('success')
                     
                    ->action(function (Get $get, Set $set) {
                            // Access unsubmitted form data
                            $formData = $get();

                            $name = $formData['name'] ?? null;
                            $source = $formData['food_source'] ?? null;
                            $foodUnit = $formData['food_unit'] ?? null;
                            $servingSize = $formData['serving_size'] ?? null;
                            
                            $foodPrompt = AI::Generate($get('name'), $get('serving_size'), $get('food_unit'), $get('food_source'));
                    

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
                            
                            // $form_labels = ['name', 'food_source', 'serving_size', 'food_unit', 'calories', 'fat', 'carbohydrates', 'protein', 'sugars', 'saturates', 'fibre']

                            $set('name', $formData['name']);
                            $set('food_source', $formData['food_source']);
                            $set('serving_size', $formData['serving_size']);
                            $set('food_unit', $formData['food_unit']);
                            $set('calories', $calculatedCalories);
                            $set('fat', $calculatedFats);
                            $set('carbohydrates', $calculatedCarbs);
                            $set('protein', $calculatedProtein);
                            $set('sugars', $calculatedSugars);
                            $set('saturates', $calculatedSaturates);
                            $set('fibre', $calculatedFibre);
                            $set('salt', $calculatedSalt);
                            $set('user_id', $formData['user_id']);
                            $set('description', "🔎 AI. Source: $sourceData");

                            Notification::make()
                                ->title('Macros estimated and filled in!')
                                ->success()
                                ->send();
                        }),
                    ]),

               

              Fieldset::make('Step 4. Nutrition: Macronutrients')
                    // ->description('These do NOT have to be filled in for AI Auto Fill.')
                    ->schema([
                        Forms\Components\TextInput::make('calories')
                            ->label('Calories (kcal)')
                            ->numeric()
                            ->default(fn ($record) => $record?->macronutrients?->calories) // <-- THIS
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),

                        Forms\Components\TextInput::make('fat')
                            ->label('Fat (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->macronutrients?->fat)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),


                        Forms\Components\TextInput::make('carbohydrates')
                            ->label('Carbohydrates (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->macronutrients?->carbohydrates)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),

                        Forms\Components\TextInput::make('protein')
                            ->label('Protein (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->macronutrients?->protein)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),
                    ]),
                    
                Fieldset::make('Step 5. Nutrition: Micronutrients')
                    // ->description('These do NOT have to be filled in for AI Auto Fill.')
                    ->schema([
                        Forms\Components\TextInput::make('sugars')
                            ->label('Sugars (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->micronutrients?->sugars) // <-- THIS
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),

                        Forms\Components\TextInput::make('saturates')
                            ->label('Saturated Fat (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->micronutrients?->saturates)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),


                        Forms\Components\TextInput::make('fibre')
                            ->label('Fibre (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->micronutrients?->fibre)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),

                        Forms\Components\TextInput::make('salt')
                            ->label('Salt (g)')
                            ->numeric()
                            ->default(fn ($record) => $record?->micronutrients?->salt)
                            ->dehydrateStateUsing(fn ($state) => (float) $state) // Save clean
                            ->required(false),
                    ]),
        
                ]);
    }

    public static function create(): CreateRecord
    {

    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(['name', 'id']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('source.name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at'),

                Tables\Columns\TextColumn::make('macronutrients.calories')
                ->label('Calories'),

                
                Tables\Columns\TextColumn::make('macronutrients.fat')
                ->label('Fat (g)'),

                Tables\Columns\TextColumn::make('macronutrients.carbohydrates')
                ->label('Carbs (g)'),
                
                Tables\Columns\TextColumn::make('macronutrients.protein')
                ->label('Protein (g)'),
                // Tables\Columns\TextColumn::make('updated_at'),

                Tables\Columns\TextColumn::make('user.name')
                    
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('viewNutrients')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => "Meal Items for: {$record->title}")
                    ->form(function ($record) {
                        $query = Macronutrients::where('macronutrients.food_id', $record->id)
                                    ->select('calories', 'fat', 'carbohydrates', 'protein');

                        // Check if micronutrients exist for this food
                        if (Micronutrients::where('food_id', $record->id)->exists()) {
                            $query->join('micronutrients', 'macronutrients.food_id', '=', 'micronutrients.food_id')
                                ->select('calories', 'fat', 'carbohydrates', 'protein')
                                ->addSelect('sugars', 'saturates', 'fibre', 'salt');
                        }

                        $nutrients = $query->first();

                        return [
                            Placeholder::make('meal_items')
                                ->label("Macros and Micros of {$record->id}")
                                ->content(new HtmlString('' . 
                                    $nutrients
                                        ? '<ul>' . collect($nutrients->toArray())
                                            ->map(fn($value, $key) => "<li><strong>" . ucfirst($key) . "</strong>: {$value}</li>")
                                            ->implode('') . '</ul>'
                                        : 'No nutrition data found.'
                                ))
                              
                        ];
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    protected function afterSave(): void
    {
        $macronutrients = $this->record->macronutrients;

            if ($macronutrients) {
                $macronutrients->update([
                    'food_unit_id' => $this->form->getState()['food_unit'],
                    'serving_size' => $this->form->getState()['serving_size'],
                    'calories' => $this->form->getState()['calories'],
                    'fat' => $this->form->getState()['fat'],
                    'carbohydrates' => $this->form->getState()['carbohydrates'],
                    'protein' => $this->form->getState()['protein'],
                ]);
            } else {
                // If Macronutrients doesn't exist, create one
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

    // --- 🎉 FANCY SUCCESS TOAST ---
        $this->notify('success', 'Macronutrients saved successfully!');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }


}
