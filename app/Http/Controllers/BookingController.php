<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
  public function __construct(private BookingService $bookingService) {}

    public function index()
    {
        $bookings = Booking::with(['vehicle', 'driver', 'approvals.approver'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('bookings/Index', ['bookings' => $bookings]);
    }

    public function create()
    {
        return Inertia::render('bookings/Create', [
            'vehicles' => Vehicle::where('status', 'available')
                ->select('id', 'plate_number', 'brand', 'model', 'type', 'capacity')->get(),
            'drivers'  => Driver::where('status', 'available')->where('is_active', true)
                ->select('id', 'name', 'license_type', 'license_number')->get(),
            'approvers' => User::role('approver')->where('is_active', true)
                ->select('id', 'name', 'position', 'department')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_id'       => 'nullable|exists:drivers,id',
            'purpose'         => 'required|string|max:255',
            'destination'     => 'required|string|max:255',
            'start_date'      => 'required|date|after:now',
            'end_date'        => 'required|date|after:start_date',
            'passenger_count' => 'required|integer|min:1',
            'notes'           => 'nullable|string',
            'approvers'       => 'required|array|min:2',
            'approvers.*.id'  => 'required|exists:users,id',
        ]);

        $booking = Booking::create([
            'user_id'         => Auth::id(),
            'vehicle_id'      => $request->vehicle_id,
            'driver_id'       => $request->driver_id,
            'purpose'         => $request->purpose,
            'destination'     => $request->destination,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'passenger_count' => $request->passenger_count,
            'notes'           => $request->notes,
            'status'          => 'pending',
        ]);

        // Buat approval berjenjang
        foreach ($request->approvers as $index => $approver) {
            $booking->approvals()->create([
                'approver_id' => $approver['id'],
                'level'       => $index + 1,
                'status'      => 'pending',
            ]);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Pemesanan berhasil dibuat dan menunggu persetujuan.');
    }

    public function show(Booking $booking)
    {
        Gate::authorize('view', $booking);
        $booking->load(['vehicle', 'driver', 'user', 'approvals.approver']);
        return Inertia::render('bookings/Show', ['booking' => $booking]);
    }

    public function complete(Request $request, Booking $booking)
    {
        $request->validate([
            'total_km'  => 'required|integer|min:0',
            'fuel_cost' => 'required|numeric|min:0',
        ]);

        $this->bookingService->completeTrip($booking, $request->total_km, $request->fuel_cost);

        return back()->with('success', 'Perjalanan berhasil diselesaikan.');
    }
}
