import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

export default function ReportIndex() {
    const [form, setForm] = useState({
        start_date: '',
        end_date: '',
        status: '',
    });

    const handleExport = () => {
        const params = new URLSearchParams();
        if (form.start_date) params.append('start_date', form.start_date);
        if (form.end_date) params.append('end_date', form.end_date);
        if (form.status) params.append('status', form.status);
        window.location.href = `/reports/export?${params.toString()}`;
    };

    return (
        <AppLayout title="Laporan Pemesanan">
            <div className="max-w-xl">
                <div className="card">
                    <h3 className="mb-1 font-semibold text-white">
                        Export Laporan Excel
                    </h3>
                    <p className="mb-6 text-sm text-gray-500">
                        Filter data pemesanan lalu download sebagai file .xlsx
                    </p>

                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="mb-1 block text-sm text-gray-400">
                                    Tanggal Mulai
                                </label>
                                <input
                                    type="date"
                                    className="form-input"
                                    value={form.start_date}
                                    onChange={(e) =>
                                        setForm((f) => ({
                                            ...f,
                                            start_date: e.target.value,
                                        }))
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
                                        setForm((f) => ({
                                            ...f,
                                            end_date: e.target.value,
                                        }))
                                    }
                                />
                            </div>
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Status
                            </label>
                            <select
                                className="form-input"
                                value={form.status}
                                onChange={(e) =>
                                    setForm((f) => ({
                                        ...f,
                                        status: e.target.value,
                                    }))
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
                        <button
                            onClick={handleExport}
                            className="btn-primary w-full"
                        >
                            📥 Download Excel
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
