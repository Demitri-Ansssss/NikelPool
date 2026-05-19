<?php

namespace App\Filament\Resources\Users;

use Filament\Forms;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions;
use App\Filament\Resources\Users\Pages;
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Data Pengguna')->schema([
                Forms\Components\TextInput::make('name')->label('Nama Lengkap')->required(),
                Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')->label('Password')
                    ->password()
                    ->required(fn(string $operation) => str_contains($operation, 'CreateUser'))
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)),
                Forms\Components\TextInput::make('employee_id')->label('ID Pegawai'),
                Forms\Components\TextInput::make('department')->label('Departemen'),
                Forms\Components\TextInput::make('position')->label('Jabatan'),
                Forms\Components\TextInput::make('phone')->label('No. HP')->tel(),
                Forms\Components\Select::make('roles')->label('Role')
                    ->options(Role::pluck('name', 'name'))
                    ->multiple()->preload(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
            Tables\Columns\TextColumn::make('department')->label('Departemen'),
            Tables\Columns\TextColumn::make('position')->label('Jabatan'),
            Tables\Columns\TextColumn::make('roles.name')->label('Role')->badge(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
        ])
        ->actions([Actions\EditAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}