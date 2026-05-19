<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('employee_id'),
                TextInput::make('license_number')
                    ->required(),
                TextInput::make('license_type')
                    ->required(),
                DatePicker::make('license_expiry')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('region_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('available'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
