<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate = null,
        private ?string $status = null,
    ) {}

    public function query()
    {
        return Booking::query()
            ->with(['user', 'vehicle', 'driver'])
            ->when($this->startDate, fn($q) => $q->whereDate('start_date', '>=', $this->startDate))
            ->when($this->endDate,   fn($q) => $q->whereDate('start_date', '<=', $this->endDate))
            ->when($this->status,    fn($q) => $q->where('status', $this->status))
            ->orderBy('start_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Pemesanan', 'Pemesan', 'Departemen', 'Kendaraan',
            'Driver', 'Keperluan', 'Tujuan',
            'Tgl Berangkat', 'Tgl Kembali', 'Penumpang',
            'Status', 'Total KM', 'Biaya BBM', 'Catatan',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->booking_number,
            $booking->user->name,
            $booking->user->department,
            "{$booking->vehicle->plate_number} - {$booking->vehicle->brand} {$booking->vehicle->model}",
            $booking->driver?->name ?? '-',
            $booking->purpose,
            $booking->destination,
            $booking->start_date->format('d/m/Y H:i'),
            $booking->end_date->format('d/m/Y H:i'),
            $booking->passenger_count,
            strtoupper($booking->status),
            $booking->total_km ?? 0,
            $booking->fuel_cost ?? 0,
            $booking->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => 'D97706']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string { return 'Laporan Pemesanan'; }
}