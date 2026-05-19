import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

interface Vehicle {
    id: number;
    plate_number: string;
    brand: string;
    model: string;
    type: string;
}
interface Driver {
    id: number;
    name: string;
    license_type: string;
}
interface Approver {
    id: number;
    name: string;
    position: string;
    department: string;
}

interface Props {
    vehicles: Vehicle[];
    drivers: Driver[];
    approvers: Approver[];
}

export default function BookingCreate({ vehicles, drivers, approvers }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        vehicle_id: '',
        driver_id: '',
        purpose: '',
        destination: '',
        start_date: '',
        end_date: '',
        passenger_count: 1,
        notes: '',
        approvers: [{ id: '' }, { id: '' }], // minimal 2 level
    });

    const addApprover = () =>
        setData('approvers', [...data.approvers, { id: '' }]);
    const removeApprover = (i: number) =>
        setData(
            'approvers',
            data.approvers.filter((_, idx) => idx !== i),
        );
    const setApprover = (i: number, id: string) => {
        const updated = [...data.approvers];
        updated[i] = { id };
        setData('approvers', updated);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/bookings');
    };

    return (
        <AppLayout title="Buat Pemesanan Kendaraan">
            <form onSubmit={submit} className="max-w-3xl space-y-6">
                {/* Kendaraan & Driver */}
                <div className="card">
                    <h3 className="mb-4 font-semibold text-white">
                        Pilih Kendaraan & Driver
                    </h3>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Kendaraan{' '}
                                <span className="text-red-400">*</span>
                            </label>
                            <select
                                className="form-input"
                                value={data.vehicle_id}
                                onChange={(e) =>
                                    setData('vehicle_id', e.target.value)
                                }
                                required
                            >
                                <option value="">-- Pilih Kendaraan --</option>
                                {vehicles.map((v) => (
                                    <option key={v.id} value={v.id}>
                                        {v.plate_number} — {v.brand} {v.model}
                                    </option>
                                ))}
                            </select>
                            {errors.vehicle_id && (
                                <p className="mt-1 text-xs text-red-400">
                                    {errors.vehicle_id}
                                </p>
                            )}
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Driver
                            </label>
                            <select
                                className="form-input"
                                value={data.driver_id}
                                onChange={(e) =>
                                    setData('driver_id', e.target.value)
                                }
                            >
                                <option value="">-- Tanpa Driver --</option>
                                {drivers.map((d) => (
                                    <option key={d.id} value={d.id}>
                                        {d.name} ({d.license_type})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>

                {/* Detail Perjalanan */}
                <div className="card">
                    <h3 className="mb-4 font-semibold text-white">
                        Detail Perjalanan
                    </h3>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Keperluan{' '}
                                <span className="text-red-400">*</span>
                            </label>
                            <input
                                className="form-input"
                                value={data.purpose}
                                onChange={(e) =>
                                    setData('purpose', e.target.value)
                                }
                                required
                                placeholder="Contoh: Rapat di site B"
                            />
                            {errors.purpose && (
                                <p className="mt-1 text-xs text-red-400">
                                    {errors.purpose}
                                </p>
                            )}
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Tujuan <span className="text-red-400">*</span>
                            </label>
                            <input
                                className="form-input"
                                value={data.destination}
                                onChange={(e) =>
                                    setData('destination', e.target.value)
                                }
                                required
                                placeholder="Contoh: Tambang Site B, Morowali"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Tanggal Berangkat{' '}
                                <span className="text-red-400">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                className="form-input"
                                value={data.start_date}
                                onChange={(e) =>
                                    setData('start_date', e.target.value)
                                }
                                required
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Tanggal Kembali{' '}
                                <span className="text-red-400">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                className="form-input"
                                value={data.end_date}
                                onChange={(e) =>
                                    setData('end_date', e.target.value)
                                }
                                required
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Jumlah Penumpang
                            </label>
                            <input
                                type="number"
                                min={1}
                                className="form-input"
                                value={data.passenger_count}
                                onChange={(e) =>
                                    setData(
                                        'passenger_count',
                                        Number(e.target.value),
                                    )
                                }
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm text-gray-400">
                                Catatan
                            </label>
                            <input
                                className="form-input"
                                value={data.notes}
                                onChange={(e) =>
                                    setData('notes', e.target.value)
                                }
                                placeholder="Opsional"
                            />
                        </div>
                    </div>
                </div>

                {/* Persetujuan Berjenjang */}
                <div className="card">
                    <h3 className="mb-1 font-semibold text-white">
                        Pihak Penyetuju <span className="text-red-400">*</span>
                    </h3>
                    <p className="mb-4 text-xs text-gray-500">
                        Minimal 2 level persetujuan diperlukan
                    </p>
                    <div className="space-y-3">
                        {data.approvers.map((a, i) => (
                            <div key={i} className="flex items-center gap-3">
                                <span className="w-20 text-xs font-semibold text-amber-400">
                                    Level {i + 1}
                                </span>
                                <select
                                    className="form-input flex-1"
                                    value={a.id}
                                    onChange={(e) =>
                                        setApprover(i, e.target.value)
                                    }
                                    required
                                >
                                    <option value="">
                                        -- Pilih Pejabat --
                                    </option>
                                    {approvers.map((ap) => (
                                        <option key={ap.id} value={ap.id}>
                                            {ap.name} — {ap.position} (
                                            {ap.department})
                                        </option>
                                    ))}
                                </select>
                                {i >= 2 && (
                                    <button
                                        type="button"
                                        onClick={() => removeApprover(i)}
                                        className="px-2 text-sm text-red-400 hover:text-red-300"
                                    >
                                        ✕
                                    </button>
                                )}
                            </div>
                        ))}
                    </div>
                    <button
                        type="button"
                        onClick={addApprover}
                        className="mt-3 text-sm text-amber-400 hover:text-amber-300"
                    >
                        + Tambah Level
                    </button>
                </div>

                <div className="flex gap-3">
                    <button
                        type="submit"
                        disabled={processing}
                        className="btn-primary"
                    >
                        {processing ? 'Menyimpan...' : 'Kirim Pemesanan'}
                    </button>
                    <a
                        href="/bookings"
                        className="rounded-lg border border-gray-700 px-6 py-2.5 text-sm font-semibold text-gray-400 transition-colors hover:bg-gray-800"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </AppLayout>
    );
}
