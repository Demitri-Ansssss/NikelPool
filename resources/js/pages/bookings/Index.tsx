import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    pending: {
        label: 'Menunggu',
        color: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    },
    approved: {
        label: 'Disetujui',
        color: 'bg-green-500/20 text-green-400 border-green-500/30',
    },
    in_progress: {
        label: 'Berjalan',
        color: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    },
    completed: {
        label: 'Selesai',
        color: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    },
    rejected: {
        label: 'Ditolak',
        color: 'bg-red-500/20 text-red-400 border-red-500/30',
    },
    cancelled: {
        label: 'Dibatalkan',
        color: 'bg-red-900/20 text-red-500 border-red-900/30',
    },
};

interface Booking {
    id: number;
    booking_number: string;
    destination: string;
    purpose: string;
    start_date: string;
    end_date: string;
    status: string;
    vehicle: { plate_number: string; brand: string; model: string };
}

export default function BookingIndex({
    bookings,
}: {
    bookings: { data: Booking[]; links: any[] };
}) {
    return (
        <AppLayout title="Daftar Pemesanan">
            <div className="mb-6 flex items-center justify-between">
                <p className="text-sm text-gray-400">
                    Total: {bookings.data.length} pemesanan
                </p>
                <Link href="/bookings/create" className="btn-primary">
                    + Buat Pemesanan
                </Link>
            </div>

            <div className="card">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-800 text-left">
                                <th className="pb-3 font-medium text-gray-400">
                                    No. Pemesanan
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Kendaraan
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Keperluan
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Tujuan
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Tgl Berangkat
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Status
                                </th>
                                <th className="pb-3 font-medium text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-800">
                            {bookings.data.map((b) => {
                                const s = STATUS_MAP[b.status] ?? {
                                    label: b.status,
                                    color: 'bg-gray-800 text-gray-400',
                                };

                                return (
                                    <tr
                                        key={b.id}
                                        className="transition-colors hover:bg-gray-800/50"
                                    >
                                        <td className="py-3 font-mono text-amber-400">
                                            {b.booking_number}
                                        </td>
                                        <td className="py-3 text-white">
                                            {b.vehicle.plate_number}{' '}
                                            <span className="text-gray-400">
                                                — {b.vehicle.brand}{' '}
                                                {b.vehicle.model}
                                            </span>
                                        </td>
                                        <td className="py-3 text-gray-300">
                                            {b.purpose}
                                        </td>
                                        <td className="py-3 text-gray-300">
                                            {b.destination}
                                        </td>
                                        <td className="py-3 text-gray-400">
                                            {new Date(
                                                b.start_date,
                                            ).toLocaleDateString('id-ID')}
                                        </td>
                                        <td className="py-3">
                                            <span
                                                className={`rounded-full border px-2 py-1 text-xs font-medium ${s.color}`}
                                            >
                                                {s.label}
                                            </span>
                                        </td>
                                        <td className="py-3">
                                            <Link
                                                href={`/bookings/${b.id}`}
                                                className="text-xs text-amber-400 hover:text-amber-300"
                                            >
                                                Detail →
                                            </Link>
                                        </td>
                                    </tr>
                                );
                            })}
                            {bookings.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={7}
                                        className="py-10 text-center text-gray-500"
                                    >
                                        Belum ada pemesanan
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
