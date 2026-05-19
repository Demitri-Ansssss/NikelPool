import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';

interface Props {
    children: ReactNode;
    title?: string;
}

export default function AppLayout({ children, title }: Props) {
    const { auth } = usePage().props as any;

    const navLinks = [
        { href: '/dashboard', label: 'Dashboard', icon: '📊' },
        { href: '/bookings', label: 'Pemesanan', icon: '🚗' },
        { href: '/approvals', label: 'Persetujuan', icon: '✅' },
        { href: '/reports', label: 'Laporan', icon: '📋' },
    ];

    return (
        <div className="flex min-h-screen bg-gray-950 text-gray-100">
            {/* Sidebar */}
            <aside className="fixed flex h-full w-64 flex-col border-r border-amber-500/20 bg-gray-900">
                <div className="border-b border-amber-500/20 p-6">
                    <h1 className="text-xl font-bold text-amber-400">
                        ⛏ NikelPool
                    </h1>
                    <p className="mt-1 text-xs text-gray-400">
                        Vehicle Management System
                    </p>
                </div>
                <nav className="flex-1 space-y-1 p-4">
                    {navLinks.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-300 transition-all hover:bg-amber-500/10 hover:text-amber-400"
                        >
                            <span>{link.icon}</span>
                            <span>{link.label}</span>
                        </Link>
                    ))}
                </nav>
                <div className="border-t border-amber-500/20 p-4">
                    <p className="text-xs text-gray-400">{auth?.user?.name}</p>
                    <p className="text-xs text-gray-500">
                        {auth?.user?.position}
                    </p>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        className="mt-2 text-xs text-red-400 hover:text-red-300"
                    >
                        Keluar
                    </Link>
                </div>
            </aside>

            {/* Main Content */}
            <main className="ml-64 flex-1 p-8">
                {title && (
                    <h2 className="mb-6 text-2xl font-bold text-white">
                        {title}
                    </h2>
                )}
                {children}
            </main>
        </div>
    );
}
