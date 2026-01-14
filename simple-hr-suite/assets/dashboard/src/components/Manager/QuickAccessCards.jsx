import React from 'react';

/**
 * Quick Access Cards - Tile-style navigation for main system modules
 * Inspired by Phoenix dashboard design patterns
 */
export default function QuickAccessCards() {
    const modules = [
        {
            id: 'sales',
            title: 'Sales',
            subtitle: 'Revenue & Orders',
            icon: ShoppingBagIcon,
            stats: { value: 'SAR 125K', label: 'This Month' },
            gradient: 'from-blue-500 to-blue-600',
            href: '#sales-section',
        },
        {
            id: 'orders',
            title: 'Orders',
            subtitle: 'Track & Manage',
            icon: ClipboardListIcon,
            stats: { value: '248', label: 'Pending' },
            gradient: 'from-emerald-500 to-emerald-600',
            href: '#orders-section',
        },
        {
            id: 'hr',
            title: 'HR',
            subtitle: 'Team & Attendance',
            icon: UsersIcon,
            stats: { value: '45', label: 'Employees' },
            gradient: 'from-violet-500 to-violet-600',
            href: '#hr-section',
        },
        {
            id: 'reports',
            title: 'Reports',
            subtitle: 'Analytics & Data',
            icon: ChartBarIcon,
            stats: { value: '12', label: 'New Reports' },
            gradient: 'from-amber-500 to-orange-500',
            href: '#reports-section',
        },
        {
            id: 'inventory',
            title: 'Inventory',
            subtitle: 'Stock & Products',
            icon: CubeIcon,
            stats: { value: '1,234', label: 'Products' },
            gradient: 'from-pink-500 to-rose-500',
            href: '#inventory-section',
        },
        {
            id: 'customers',
            title: 'Customers',
            subtitle: 'CRM & Contacts',
            icon: UserGroupIcon,
            stats: { value: '5.2K', label: 'Active' },
            gradient: 'from-cyan-500 to-teal-500',
            href: '#customers-section',
        },
    ];

    return (
        <div className="mb-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                    Quick Access
                </h2>
                <a href="#" className="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                    Customize
                </a>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                {modules.map((module) => {
                    const Icon = module.icon;
                    return (
                        <a
                            key={module.id}
                            href={module.href}
                            className="group relative overflow-hidden rounded-xl p-4 transition-all duration-300 hover:scale-105 hover:shadow-lg"
                        >
                            {/* Gradient background */}
                            <div className={`absolute inset-0 bg-gradient-to-br ${module.gradient} opacity-90 group-hover:opacity-100 transition-opacity`} />

                            {/* Content */}
                            <div className="relative z-10 text-white">
                                <div className="flex items-center justify-between mb-3">
                                    <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <Icon className="h-5 w-5" />
                                    </div>
                                    <ArrowIcon className="h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-0 group-hover:translate-x-1" />
                                </div>

                                <h3 className="font-semibold text-sm mb-0.5">
                                    {module.title}
                                </h3>
                                <p className="text-xs text-white/80 mb-3">
                                    {module.subtitle}
                                </p>

                                <div className="pt-2 border-t border-white/20">
                                    <p className="text-lg font-bold">
                                        {module.stats.value}
                                    </p>
                                    <p className="text-xs text-white/70">
                                        {module.stats.label}
                                    </p>
                                </div>
                            </div>

                            {/* Decorative circle */}
                            <div className="absolute -bottom-4 -right-4 w-20 h-20 bg-white/10 rounded-full" />
                        </a>
                    );
                })}
            </div>
        </div>
    );
}

// Icons
function ShoppingBagIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
    );
}

function ClipboardListIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
    );
}

function UsersIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );
}

function ChartBarIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
    );
}

function CubeIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    );
}

function UserGroupIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    );
}

function ArrowIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    );
}
