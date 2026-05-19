<?php

namespace App\Filament\Resources\Vehicles;

use App\Filament\Resources\Vehicles\Pages;
use App\Models\Driver;
use App\Models\Region;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Actions;

class VehicleResource extends Resource
{
   protected static ?string $model = Vehicle::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Kendaraan';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Informasi Kendaraan')->schema([
                Forms\Components\TextInput::make('plate_number')->label('No. Polisi')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('brand')->label('Merek')->required(),
                Forms\Components\TextInput::make('model')->label('Model')->required(),
                Forms\Components\TextInput::make('year')->label('Tahun')->numeric()->required(),
                Forms\Components\Select::make('type')->label('Jenis Kendaraan')->required()
                    ->options(['angkutan_orang' => 'Angkutan Orang', 'angkutan_barang' => 'Angkutan Barang']),
                Forms\Components\Select::make('ownership')->label('Kepemilikan')->required()
                    ->options(['milik' => 'Milik Perusahaan', 'sewa' => 'Sewa'])
                    ->reactive(),
                Forms\Components\TextInput::make('rental_company')->label('Perusahaan Sewa')
                    ->hidden(fn ($get) => $get('ownership') !== 'sewa'),
                Forms\Components\Select::make('fuel_type')->label('Jenis BBM')->required()
                    ->options(['bensin' => 'Bensin', 'solar' => 'Solar', 'listrik' => 'Listrik']),
                Forms\Components\TextInput::make('capacity')->label('Kapasitas (orang/ton)')->numeric(),
                Forms\Components\TextInput::make('current_km')->label('KM Saat Ini')->numeric()->default(0),
                Forms\Components\Select::make('region_id')->label('Lokasi')
                    ->options(Region::pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('status')->label('Status')
                    ->options(['available' => 'Tersedia', 'in_use' => 'Digunakan', 'maintenance' => 'Servis', 'retired' => 'Pensiunkan'])
                    ->default('available'),
                Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('plate_number')->label('No. Polisi')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('brand')->label('Merek'),
            Tables\Columns\TextColumn::make('model')->label('Model'),
            Tables\Columns\BadgeColumn::make('type')->label('Jenis')
                ->formatStateUsing(fn($s) => $s === 'angkutan_orang' ? 'Orang' : 'Barang')
                ->colors(['primary' => 'angkutan_orang', 'warning' => 'angkutan_barang']),
            Tables\Columns\BadgeColumn::make('ownership')->label('Kepemilikan')
                ->colors(['success' => 'milik', 'info' => 'sewa']),
            Tables\Columns\TextColumn::make('region.name')->label('Lokasi'),
            Tables\Columns\BadgeColumn::make('status')->label('Status')
                ->colors(['success' => 'available', 'warning' => 'in_use', 'danger' => 'maintenance', 'gray' => 'retired']),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')->options(['angkutan_orang' => 'Orang', 'angkutan_barang' => 'Barang']),
            Tables\Filters\SelectFilter::make('status')->options(['available' => 'Tersedia', 'in_use' => 'Digunakan', 'maintenance' => 'Servis']),
            Tables\Filters\SelectFilter::make('region_id')->label('Lokasi')->relationship('region', 'name'),
        ])
        ->actions([Actions\EditAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit'   => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
