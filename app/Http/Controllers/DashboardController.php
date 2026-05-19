<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $stats = [
            'total_vehicles'   => Vehicle::count(),
            'available'        => Vehicle::where('status', 'available')->count(),
            'in_use'           => Vehicle::where('status', 'in_use')->count(),
            'maintenance'      => Vehicle::where('status', 'maintenance')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_drivers'    => Driver::where('is_active', true)->count(),
        ];

        // Grafik: pemesanan per bulan (12 bulan terakhir)
        $monthlyBookings = Booking::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Grafik: pemesanan per status (pie chart)
        $statusBreakdown = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->get();

        // Grafik: kendaraan paling sering dipakai (top 5)
        $topVehicles = Booking::select('vehicle_id', DB::raw('COUNT(*) as total'))
            ->with('vehicle:id,plate_number,brand,model')
            ->where('status', 'completed')
            ->groupBy('vehicle_id')
            ->orderByDesc('total')
            ->take(5)->get();

        return Inertia::render('dashboard/Index', compact(
            'stats', 'monthlyBookings', 'statusBreakdown', 'topVehicles'
        ));
    }
}
