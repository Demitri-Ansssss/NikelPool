<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Pemesanan';    
    protected static ?string $modelLabel = 'Pemesanan';
    protected static ?int $navigationSort = 1;  

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Detail Pemesanan')->schema([
                Forms\Components\Select::make('vehicle_id')->label('Kendaraan')->required()
                    ->options(Vehicle::where('status', 'available')->get()->mapWithKeys(
                        fn($v) => [$v->id => "{$v->plate_number} — {$v->brand} {$v->model}"]
                    ))->searchable(),
                Forms\Components\Select::make('driver_id')->label('Driver')
                    ->options(Driver::where('status', 'available')->where('is_active', true)
                        ->get()->mapWithKeys(fn($d) => [$d->id => "{$d->name} ({$d->license_type})"])
                    )->searchable()->nullable(),
                Forms\Components\TextInput::make('purpose')->label('Keperluan')->required(),
                Forms\Components\TextInput::make('destination')->label('Tujuan')->required(),
                Forms\Components\DateTimePicker::make('start_date')->label('Tanggal Berangkat')->required(),
                Forms\Components\DateTimePicker::make('end_date')->label('Tanggal Kembali')->required(),
                Forms\Components\TextInput::make('passenger_count')->label('Jumlah Penumpang')->numeric()->default(1),
                Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])->columns(2),

                Section::make('Pihak yang Menyetujui')->schema([
                Forms\Components\Repeater::make('approvals')->label('Level Persetujuan')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('level')->label('Level')->numeric()->disabled(),
                        Forms\Components\Select::make('approver_id')->label('Pejabat Penyetuju')->required()
                            ->options(User::role('approver')->pluck('name', 'id')),
                    ])
                    ->columns(2)
                    ->defaultItems(2)
                    ->minItems(2)
                    ->addActionLabel('Tambah Level Persetujuan')
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $index): array {
                        $data['level'] = $index + 1;
                        return $data;
                    }),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('booking_number')->label('No. Pemesanan')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('user.name')->label('Pemesan'),
            Tables\Columns\TextColumn::make('vehicle.plate_number')->label('Kendaraan'),
            Tables\Columns\TextColumn::make('destination')->label('Tujuan'),
            Tables\Columns\TextColumn::make('start_date')->label('Tgl Berangkat')->dateTime('d/m/Y H:i'),
            Tables\Columns\TextColumn::make('end_date')->label('Tgl Kembali')->dateTime('d/m/Y H:i'),
            Tables\Columns\BadgeColumn::make('status')->label('Status')
                ->colors([
                    'gray'    => 'pending',
                    'warning' => 'approved',
                    'success' => 'completed',
                    'danger'  => 'rejected',
                    'info'    => 'in_progress',
                ]),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                'pending'     => 'Pending',
                'approved'    => 'Disetujui',
                'in_progress' => 'Sedang Berjalan',
                'completed'   => 'Selesai',
                'rejected'    => 'Ditolak',
            ]),
        ])
        ->actions([
            Actions\ViewAction::make(),
            Actions\EditAction::make(),
        ])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
