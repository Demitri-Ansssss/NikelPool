<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        // User bisa lihat booking miliknya sendiri
        // Atau approver yang ada di daftar approval booking ini
        return $user->id === $booking->user_id
            || $booking->approvals()->where('approver_id', $user->id)->exists()
            || $user->hasRole('admin');
    }
}