<?php

namespace App\Filament\Resources\Drivers;

use App\Filament\Resources\Drivers\Pages;
use App\Models\Driver;
use App\Models\Region;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Actions;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Driver';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Data Driver')->schema([
                Forms\Components\TextInput::make('name')->label('Nama Lengkap')->required(),
                Forms\Components\TextInput::make('employee_id')->label('ID Pegawai'),
                Forms\Components\TextInput::make('license_number')->label('No. SIM')->required(),
                Forms\Components\Select::make('license_type')->label('Tipe SIM')->required()
                    ->options(['A' => 'SIM A', 'B1' => 'SIM B1', 'B2' => 'SIM B2', 'C' => 'SIM C']),
                Forms\Components\DatePicker::make('license_expiry')->label('Berlaku Sampai')->required(),
                Forms\Components\TextInput::make('phone')->label('No. HP')->tel(),
                Forms\Components\Select::make('region_id')->label('Lokasi')
                    ->options(Region::pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('status')->label('Status')
                    ->options(['available' => 'Tersedia', 'on_duty' => 'Bertugas', 'off' => 'Libur'])
                    ->default('available'),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('license_number')->label('No. SIM'),
            Tables\Columns\TextColumn::make('license_type')->label('Tipe SIM'),
            Tables\Columns\TextColumn::make('license_expiry')->label('Exp. SIM')->date('d/m/Y'),
            Tables\Columns\TextColumn::make('region.name')->label('Lokasi'),
            Tables\Columns\BadgeColumn::make('status')->label('Status')
                ->colors(['success' => 'available', 'warning' => 'on_duty', 'danger' => 'off']),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
        ])
        ->actions([Actions\EditAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit'   => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
