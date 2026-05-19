<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BookingExport;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('reports/Index');
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
