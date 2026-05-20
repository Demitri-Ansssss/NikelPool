import { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function ReportIndex({ bookings, filters }: any) {
    const [form, setForm] = useState({
        start_date: filters?.start_date || '',
        end_date: filters?.end_date || '',
        status: filters?.status || '',
    });

    const handleFilter = () => {
        router.get('/reports', form, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const handleExport = () => {
        const params = new URLSearchParams();
        if (form.start_date) params.append('start_date', form.start_date);
        if (form.end_date) params.append('end_date', form.end_date);
        if (form.status) params.append('status', form.status);
        window.location.href = `/reports/export?${params.toString()}`;
    };

    return (
        <AppLayout title="Laporan Pemesanan">
            <div className="space-y-6">
                <div className="card">
                    <h3 className="mb-1 font-semibold text-white">
                        Filter Laporan
                    </h3>
                    <p className="mb-6 text-sm text-gray-500">
                        Filter data pemesanan untuk dilihat pada tabel atau
                        di-download sebagai file .xlsx
                    </p>

                    <div className="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Tanggal Mulai
                            </label>
                            <input
                                type="date"
                                className="form-input"
                                value={form.start_date}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        start_date: e.target.value,
                                    })
                                }
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Tanggal Akhir
                            </label>
                            <input
                                type="date"
                                className="form-input"
                                value={form.end_date}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        end_date: e.target.value,
                                    })
                                }
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Status
                            </label>
                            <select
                                className="form-input"
                                value={form.status}
                                onChange={(e) =>
                                    setForm({ ...form, status: e.target.value })
                                }
                            >
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="approved">Disetujui</option>
                                <option value="in_progress">
                                    Sedang Berjalan
                                </option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <button
                            onClick={handleFilter}
                            className="btn-primary flex-1"
                        >
                            🔍 Tampilkan Data
                        </button>
                        <button
                            onClick={handleExport}
                            className="btn-primary flex-1"
                        >
                            📥 Download Excel
                        </button>
                    </div>
                </div>

                <div className="card">
                    <h3 className="mb-4 font-semibold text-white">
                        Data Laporan Pemesanan
                    </h3>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-400">
                            <thead className="bg-gray-800/50 text-xs text-gray-400 uppercase">
                                <tr>
                                    <th className="px-4 py-3">No. Pemesanan</th>
                                    <th className="px-4 py-3">Pemesan</th>
                                    <th className="px-4 py-3">Kendaraan</th>
                                    <th className="px-4 py-3">Tgl Berangkat</th>
                                    <th className="px-4 py-3">Tgl Kembali</th>
                                    <th className="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {bookings.data.length > 0 ? (
                                    bookings.data.map((booking: any) => (
                                        <tr
                                            key={booking.id}
                                            className="border-b border-gray-800"
                                        >
                                            <td className="px-4 py-3 font-medium text-white">
                                                {booking.booking_number}
                                            </td>
                                            <td className="px-4 py-3">
                                                {booking.user?.name}
                                            </td>
                                            <td className="px-4 py-3">
                                                {booking.vehicle?.plate_number}{' '}
                                                - {booking.vehicle?.model}
                                            </td>
                                            <td className="px-4 py-3">
                                                {new Date(
                                                    booking.start_date,
                                                ).toLocaleString('id-ID')}
                                            </td>
                                            <td className="px-4 py-3">
                                                {new Date(
                                                    booking.end_date,
                                                ).toLocaleString('id-ID')}
                                            </td>
                                            <td className="px-4 py-3">
                                                <span
                                                    className={`rounded-full px-2 py-1 text-xs font-semibold ${booking.status === 'pending' ? 'bg-yellow-500/10 text-yellow-500' : ''} ${booking.status === 'approved' ? 'bg-blue-500/10 text-blue-500' : ''} ${booking.status === 'in_progress' ? 'bg-indigo-500/10 text-indigo-500' : ''} ${booking.status === 'completed' ? 'bg-green-500/10 text-green-500' : ''} ${booking.status === 'rejected' ? 'bg-red-500/10 text-red-500' : ''} ${booking.status === 'cancelled' ? 'bg-gray-500/10 text-gray-500' : ''} `}
                                                >
                                                    {booking.status.toUpperCase()}
                                                </span>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-4 py-8 text-center text-gray-500"
                                        >
                                            Tidak ada data pemesanan yang
                                            ditemukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {/* Pagination - simple implementation */}
                    {bookings.links && bookings.links.length > 3 && (
                        <div className="mt-4 flex justify-center gap-1">
                            {bookings.links.map((link: any, i: number) => (
                                <button
                                    key={i}
                                    onClick={() =>
                                        link.url &&
                                        router.get(link.url, form, {
                                            preserveState: true,
                                        })
                                    }
                                    disabled={!link.url}
                                    className={`rounded px-3 py-1 text-sm ${
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                                    } ${!link.url && 'cursor-not-allowed opacity-50'}`}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
