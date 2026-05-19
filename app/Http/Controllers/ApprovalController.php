<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Models\BookingApproval;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Tampilkan daftar approval yang perlu diproses (untuk approver)
     */
    public function index()
    {
        $approvals = BookingApproval::with(['booking.user', 'booking.vehicle', 'booking.driver'])
            ->where('approver_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $history = BookingApproval::with(['booking.user', 'booking.vehicle'])
            ->where('approver_id', Auth::id())
            ->where('status', '!=', 'pending')
            ->orderBy('responded_at', 'desc')
            ->take(20)
            ->get();

        return Inertia::render('approvals/Index', [
            'approvals' => $approvals,
            'history'   => $history,
        ]);
    }

    /**
     * Proses persetujuan (approve / reject)
     */
    public function process(Request $request, BookingApproval $approval)
    {
        // Pastikan hanya approver yang bersangkutan
        if ($approval->approver_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak memproses persetujuan ini.');
        }

        if ($approval->status !== 'pending') {
            return back()->with('error', 'Persetujuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes'  => 'nullable|string|max:500',
        ]);

        $success = $this->bookingService->processApproval(
            $approval,
            $request->status,
            $request->notes
        );

        if ($success) {
            $msg = $request->status === 'approved' ? 'Pemesanan berhasil disetujui.' : 'Pemesanan berhasil ditolak.';
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal memproses persetujuan. Silakan coba lagi.');
    }
}
