import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip,
} from 'chart.js';
import { Bar, Doughnut } from 'react-chartjs-2';
import AppLayout from '@/layouts/AppLayout';

ChartJS.register(
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
);

interface Props {
    stats: {
        total_vehicles: number;
        available: number;
        in_use: number;
        maintenance: number;
        pending_bookings: number;
        total_drivers: number;
    };
    monthlyBookings: { month: number; year: number; total: number }[];
    statusBreakdown: { status: string; total: number }[];
    topVehicles: {
        vehicle: { plate_number: string; brand: string; model: string };
        total: number;
    }[];
}

const MONTH_NAMES = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'Mei',
    'Jun',
    'Jul',
    'Agu',
    'Sep',
    'Okt',
    'Nov',
    'Des',
];

const STATUS_LABELS: Record<string, string> = {
    pending: 'Menunggu',
    approved: 'Disetujui',
    in_progress: 'Berjalan',
    completed: 'Selesai',
    rejected: 'Ditolak',
    cancelled: 'Dibatalkan',
};

export default function Dashboard({
    stats,
    monthlyBookings,
    statusBreakdown,
    topVehicles,
}: Props) {
    const statCards = [
        {
            label: 'Total Kendaraan',
            value: stats.total_vehicles,
            color: 'from-amber-500 to-orange-600',
            icon: '🚗',
        },
        {
            label: 'Tersedia',
            value: stats.available,
            color: 'from-green-500 to-emerald-600',
            icon: '✅',
        },
        {
            label: 'Sedang Dipakai',
            value: stats.in_use,
            color: 'from-blue-500 to-cyan-600',
            icon: '🔑',
        },
        {
            label: 'Dalam Servis',
            value: stats.maintenance,
            color: 'from-red-500 to-rose-600',
            icon: '🔧',
        },
        {
            label: 'Pemesanan Pending',
            value: stats.pending_bookings,
            color: 'from-purple-500 to-violet-600',
            icon: '📋',
        },
        {
            label: 'Total Driver',
            value: stats.total_drivers,
            color: 'from-teal-500 to-cyan-600',
            icon: '👤',
        },
    ];

    // Bar chart data - monthly
    const barData = {
        labels: monthlyBookings.map(
            (m) => `${MONTH_NAMES[m.month - 1]} ${m.year}`,
        ),
        datasets: [
            {
                label: 'Jumlah Pemesanan',
                data: monthlyBookings.map((m) => m.total),
                backgroundColor: 'rgba(245, 158, 11, 0.7)',
                borderColor: 'rgb(245, 158, 11)',
                borderWidth: 1,
                borderRadius: 6,
            },
        ],
    };

    // Doughnut chart - status
    const doughnutData = {
        labels: statusBreakdown.map((s) => STATUS_LABELS[s.status] ?? s.status),
        datasets: [
            {
                data: statusBreakdown.map((s) => s.total),
                backgroundColor: [
                    '#f59e0b',
                    '#10b981',
                    '#3b82f6',
                    '#6366f1',
                    '#ef4444',
                    '#6b7280',
                ],
            },
        ],
    };

    const chartOptions = {
        responsive: true,
        plugins: { legend: { labels: { color: '#d1d5db' } } },
        scales: {
            x: { ticks: { color: '#9ca3af' }, grid: { color: '#1f2937' } },
            y: { ticks: { color: '#9ca3af' }, grid: { color: '#1f2937' } },
        },
    };

    return (
        <AppLayout title="Dashboard">
            {/* Stat Cards */}
            <div className="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-3">
                {statCards.map((card) => (
                    <div
                        key={card.label}
                        className={`bg-gradient-to-br ${card.color} rounded-xl p-5 shadow-lg`}
                    >
                        <div className="mb-2 text-3xl">{card.icon}</div>
                        <p className="text-3xl font-bold text-white">
                            {card.value}
                        </p>
                        <p className="mt-1 text-sm text-white/80">
                            {card.label}
                        </p>
                    </div>
                ))}
            </div>

            {/* Charts Row */}
            <div className="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                {/* Bar Chart */}
                <div className="rounded-xl border border-gray-800 bg-gray-900 p-6 lg:col-span-2">
                    <h3 className="mb-4 font-semibold text-white">
                        Pemesanan per Bulan
                    </h3>
                    <Bar data={barData} options={chartOptions} />
                </div>

                {/* Doughnut Chart */}
                <div className="rounded-xl border border-gray-800 bg-gray-900 p-6">
                    <h3 className="mb-4 font-semibold text-white">
                        Status Pemesanan
                    </h3>
                    <Doughnut
                        data={doughnutData}
                        options={{
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#d1d5db', padding: 12 },
                                },
                            },
                        }}
                    />
                </div>
            </div>

            {/* Top Vehicles */}
            <div className="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h3 className="mb-4 font-semibold text-white">
                    Kendaraan Terbanyak Digunakan
                </h3>
                <div className="space-y-3">
                    {topVehicles.map((item, i) => (
                        <div key={i} className="flex items-center gap-4">
                            <span className="w-6 font-bold text-amber-400">
                                #{i + 1}
                            </span>
                            <div className="flex-1">
                                <p className="text-sm font-medium text-white">
                                    {item.vehicle.plate_number} —{' '}
                                    {item.vehicle.brand} {item.vehicle.model}
                                </p>
                                <div className="mt-1 h-2 w-full rounded-full bg-gray-800">
                                    <div
                                        className="h-2 rounded-full bg-amber-500 transition-all"
                                        style={{
                                            width: `${(item.total / (topVehicles[0]?.total || 1)) * 100}%`,
                                        }}
                                    />
                                </div>
                            </div>
                            <span className="font-mono text-sm text-gray-400">
                                {item.total}x
                            </span>
                        </div>
                    ))}
                    {topVehicles.length === 0 && (
                        <p className="py-4 text-center text-sm text-gray-500">
                            Belum ada data
                        </p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

Dashboard.layout = (page: any) => page;
