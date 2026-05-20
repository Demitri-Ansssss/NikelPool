import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    pending: { label: 'Menunggu', color: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' },
    approved: { label: 'Disetujui', color: 'bg-green-500/20 text-green-400 border-green-500/30' },
    in_progress: { label: 'Berjalan', color: 'bg-blue-500/20 text-blue-400 border-blue-500/30' },
    completed: { label: 'Selesai', color: 'bg-gray-500/20 text-gray-400 border-gray-500/30' },
    rejected: { label: 'Ditolak', color: 'bg-red-500/20 text-red-400 border-red-500/30' },
    cancelled: { label: 'Dibatalkan', color: 'bg-red-900/20 text-red-500 border-red-900/30' },
};

export default function BookingShow({ booking }: { booking: any }) {
    const s = STATUS_MAP[booking.status] ?? { label: booking.status, color: 'bg-gray-800 text-gray-400' };

    return (
        <AppLayout title={`Detail Pemesanan ${booking.booking_number}`}>
            <div className="mb-6 flex items-center justify-between">
                <Link href="/bookings" className="text-gray-400 hover:text-white">
                    ← Kembali ke Daftar
                </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="card space-y-4">
                    <h3 className="font-semibold text-white border-b border-gray-700 pb-2">Informasi Pemesanan</h3>
                    <div>
                        <p className="text-xs text-gray-500">Status</p>
                        <span className={`rounded-full border px-2 py-1 text-xs font-medium ${s.color}`}>
                            {s.label}
                        </span>
                    </div>
                    <div>
                        <p className="text-xs text-gray-500">Tujuan</p>
                        <p className="text-sm text-gray-300">{booking.destination}</p>
                    </div>
                    <div>
                        <p className="text-xs text-gray-500">Keperluan</p>
                        <p className="text-sm text-gray-300">{booking.purpose}</p>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <p className="text-xs text-gray-500">Tgl Berangkat</p>
                            <p className="text-sm text-gray-300">{new Date(booking.start_date).toLocaleString('id-ID')}</p>
                        </div>
                        <div>
                            <p className="text-xs text-gray-500">Tgl Kembali</p>
                            <p className="text-sm text-gray-300">{new Date(booking.end_date).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                    <div>
                        <p className="text-xs text-gray-500">Catatan</p>
                        <p className="text-sm text-gray-300">{booking.notes || '-'}</p>
                    </div>
                </div>

                <div className="card space-y-4">
                    <h3 className="font-semibold text-white border-b border-gray-700 pb-2">Informasi Kendaraan & Pengemudi</h3>
                    <div>
                        <p className="text-xs text-gray-500">Kendaraan</p>
                        <p className="text-sm text-gray-300">{booking.vehicle?.plate_number} - {booking.vehicle?.brand} {booking.vehicle?.model}</p>
                    </div>
                    <div>
                        <p className="text-xs text-gray-500">Pengemudi</p>
                        <p className="text-sm text-gray-300">{booking.driver?.name || 'Tanpa Pengemudi'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-gray-500">Pemesan</p>
                        <p className="text-sm text-gray-300">{booking.user?.name}</p>
                    </div>
                </div>
            </div>

            <div className="card mt-6">
                <h3 className="font-semibold text-white border-b border-gray-700 pb-2 mb-4">Status Persetujuan</h3>
                <div className="space-y-4">
                    {booking.approvals?.map((approval: any) => (
                        <div key={approval.id} className="flex justify-between items-center p-3 bg-gray-800/50 rounded-lg">
                            <div>
                                <p className="text-sm font-medium text-white">{approval.approver?.name}</p>
                                <p className="text-xs text-gray-500">Persetujuan Tingkat {approval.level}</p>
                            </div>
                            <div>
                                <span className={`rounded px-2 py-1 text-xs font-medium ${
                                    approval.status === 'approved' ? 'bg-green-500/20 text-green-400' :
                                    approval.status === 'rejected' ? 'bg-red-500/20 text-red-400' :
                                    'bg-yellow-500/20 text-yellow-400'
                                }`}>
                                    {approval.status.toUpperCase()}
                                </span>
                            </div>
                        </div>
                    ))}
                    {(!booking.approvals || booking.approvals.length === 0) && (
                        <p className="text-sm text-gray-500">Belum ada data persetujuan.</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
