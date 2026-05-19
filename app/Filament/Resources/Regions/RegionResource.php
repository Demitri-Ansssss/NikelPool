<?php

namespace App\Filament\Resources\Regions;

use App\Filament\Resources\Regions\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Actions;

class RegionResource extends Resource
{
   protected static ?string $model = Region::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Lokasi';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Informasi Lokasi')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lokasi')->required()->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipe')->required()
                    ->options(['pusat' => 'Kantor Pusat', 'cabang' => 'Kantor Cabang', 'tambang' => 'Tambang']),
                Forms\Components\TextInput::make('city')->label('Kota'),
                Forms\Components\TextInput::make('province')->label('Provinsi'),
                Forms\Components\Textarea::make('address')->label('Alamat')->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            Tables\Columns\BadgeColumn::make('type')->label('Tipe')
                ->colors(['primary' => 'pusat', 'warning' => 'cabang', 'success' => 'tambang']),
            Tables\Columns\TextColumn::make('city')->label('Kota'),
            Tables\Columns\TextColumn::make('province')->label('Provinsi'),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
        ])
        ->filters([Tables\Filters\SelectFilter::make('type')->options(['pusat'=>'Pusat','cabang'=>'Cabang','tambang'=>'Tambang'])])
        ->actions([Actions\EditAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit'   => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
