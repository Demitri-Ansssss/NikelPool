import { router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

interface Approval {
    id: number;
    level: number;
    status: string;
    booking: {
        booking_number: string;
        purpose: string;
        destination: string;
        start_date: string;
        end_date: string;
        user: { name: string; department: string };
        vehicle: { plate_number: string; brand: string; model: string };
        driver: { name: string } | null;
    };
}

export default function ApprovalIndex({
    approvals,
    history,
}: {
    approvals: Approval[];
    history: Approval[];
}) {
    const [selected, setSelected] = useState<Approval | null>(null);
    const [notes, setNotes] = useState('');
    const [processing, setProcessing] = useState(false);

    const process = (status: 'approved' | 'rejected') => {
        if (!selected) return;

        setProcessing(true);
        router.post(
            `/approvals/${selected.id}/process`,
            { status, notes },
            {
                onFinish: () => {
                    setProcessing(false);
                    setSelected(null);
                    setNotes('');
                },
            },
        );
    };

    return (
        <AppLayout title="Persetujuan Pemesanan">
            {/* Pending Approvals */}
            <div className="mb-8">
                <h3 className="mb-4 text-lg font-semibold text-white">
                    Menunggu Persetujuan Anda
                    <span className="ml-2 rounded-full bg-red-500 px-2 py-0.5 text-xs text-white">
                        {approvals.length}
                    </span>
                </h3>

                {approvals.length === 0 ? (
                    <div className="card py-10 text-center">
                        <p className="mb-3 text-4xl">✅</p>
                        <p className="text-gray-400">
                            Tidak ada pemesanan yang perlu disetujui
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {approvals.map((ap) => (
                            <div
                                key={ap.id}
                                className="card border-l-4 border-l-amber-500 transition-colors hover:bg-gray-800/50"
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex-1">
                                        <div className="mb-1 flex items-center gap-2">
                                            <span className="font-mono text-sm text-amber-400">
                                                {ap.booking.booking_number}
                                            </span>
                                            <span className="rounded-full border border-amber-500/30 bg-amber-500/20 px-2 py-0.5 text-xs text-amber-400">
                                                Level {ap.level}
                                            </span>
                                        </div>
                                        <p className="font-medium text-white">
                                            {ap.booking.purpose}
                                        </p>
                                        <p className="text-sm text-gray-400">
                                            Tujuan: {ap.booking.destination}
                                        </p>
                                        <div className="mt-2 grid grid-cols-3 gap-3 text-xs text-gray-500">
                                            <span>
                                                👤 {ap.booking.user.name} (
                                                {ap.booking.user.department})
                                            </span>
                                            <span>
                                                🚗{' '}
                                                {
                                                    ap.booking.vehicle
                                                        .plate_number
                                                }{' '}
                                                — {ap.booking.vehicle.brand}
                                            </span>
                                            <span>
                                                📅{' '}
                                                {new Date(
                                                    ap.booking.start_date,
                                                ).toLocaleDateString('id-ID')}
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => setSelected(ap)}
                                        className="btn-primary ml-4 text-sm"
                                    >
                                        Proses
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* History */}
            <div>
                <h3 className="mb-4 text-lg font-semibold text-white">
                    Riwayat Persetujuan
                </h3>
                <div className="card">
                    <div className="space-y-2">
                        {history.map((ap) => (
                            <div
                                key={ap.id}
                                className="flex items-center justify-between border-b border-gray-800 py-2 last:border-0"
                            >
                                <div>
                                    <span className="font-mono text-xs text-amber-400">
                                        {ap.booking.booking_number}
                                    </span>
                                    <span className="ml-2 text-sm text-gray-300">
                                        {ap.booking.purpose}
                                    </span>
                                </div>
                                <span
                                    className={`rounded-full border px-2 py-1 text-xs ${ap.status === 'approved' ? 'border-green-500/30 bg-green-500/20 text-green-400' : 'border-red-500/30 bg-red-500/20 text-red-400'}`}
                                >
                                    {ap.status === 'approved'
                                        ? 'Disetujui'
                                        : 'Ditolak'}
                                </span>
                            </div>
                        ))}
                        {history.length === 0 && (
                            <p className="py-4 text-center text-sm text-gray-500">
                                Belum ada riwayat
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Modal Proses */}
            {selected && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
                    onClick={() => setSelected(null)}
                >
                    <div
                        className="w-full max-w-md rounded-xl border border-gray-700 bg-gray-900 p-6 shadow-2xl"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <h3 className="mb-4 text-lg font-semibold text-white">
                            Proses Persetujuan
                        </h3>
                        <div className="mb-4 space-y-1 rounded-lg bg-gray-800 p-4 text-sm">
                            <p className="font-mono text-amber-400">
                                {selected.booking.booking_number}
                            </p>
                            <p className="text-white">
                                {selected.booking.purpose}
                            </p>
                            <p className="text-gray-400">
                                Tujuan: {selected.booking.destination}
                            </p>
                            <p className="text-gray-400">
                                Pemohon: {selected.booking.user.name}
                            </p>
                        </div>
                        <div className="mb-4">
                            <label className="mb-1 block text-sm text-gray-400">
                                Catatan (opsional)
                            </label>
                            <textarea
                                rows={3}
                                className="form-input resize-none"
                                value={notes}
                                onChange={(e) => setNotes(e.target.value)}
                                placeholder="Alasan persetujuan / penolakan..."
                            />
                        </div>
                        <div className="flex gap-3">
                            <button
                                disabled={processing}
                                onClick={() => process('approved')}
                                className="flex-1 rounded-lg bg-green-600 py-2.5 font-semibold text-white transition-colors hover:bg-green-700 disabled:opacity-50"
                            >
                                ✅ Setujui
                            </button>
                            <button
                                disabled={processing}
                                onClick={() => process('rejected')}
                                className="flex-1 rounded-lg bg-red-600 py-2.5 font-semibold text-white transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                ✕ Tolak
                            </button>
                        </div>
                        <button
                            onClick={() => setSelected(null)}
                            className="mt-2 w-full text-sm text-gray-500 hover:text-gray-400"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
