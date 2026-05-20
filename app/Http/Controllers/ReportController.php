<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BookingExport;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $bookings = \App\Models\Booking::query()
            ->with(['user', 'vehicle', 'driver'])
            ->when($request->start_date, fn($q) => $q->whereDate('start_date', '>=', $request->start_date))
            ->when($request->end_date,   fn($q) => $q->whereDate('start_date', '<=', $request->end_date))
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->orderBy('start_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('reports/Index', [
            'bookings' => $bookings,
            'filters'  => $request->only(['start_date', 'end_date', 'status']),
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'nullable|in:pending,approved,rejected,in_progress,completed,cancelled',
        ]);

        $filename = 'laporan-pemesanan-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new BookingExport($request->start_date, $request->end_date, $request->status),
            $filename
        );
    }
}
