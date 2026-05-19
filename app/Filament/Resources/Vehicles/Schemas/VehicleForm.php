<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plate_number')
                    ->required(),
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('ownership')
                    ->required(),
                TextInput::make('rental_company'),
                TextInput::make('capacity')
                    ->numeric(),
                TextInput::make('fuel_type')
                    ->required(),
                TextInput::make('current_km')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('region_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('available'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
