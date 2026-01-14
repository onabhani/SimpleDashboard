import React from 'react';

/**
 * Sidebar navigation component
 */
export default function Sidebar({ isOpen, onClose }) {
    const navItems = [
        {
            section: 'MAIN',
            items: [
                { label: 'Dashboard', href: '#', icon: HomeIcon, active: true },
            ],
        },
        {
            section: 'HR',
            items: [
                { label: 'My HR', href: '#my-hr', icon: UserIcon },
                { label: 'My Team', href: '#team-snapshot', icon: UsersIcon },
                { label: 'HR Actions', href: '#hr-actions', icon: ClipboardIcon },
            ],
        },
        {
            section: 'WORKFLOWS',
            items: [
                { label: 'My Tasks', href: '#workflows', icon: TasksIcon },
            ],
        },
        {
            section: 'SALES',
            disabled: true,
            items: [
                { label: 'Sales Overview', href: '#', icon: ChartIcon, disabled: true },
                { label: 'Customers / Orders', href: '#', icon: ShoppingIcon, disabled: true },
            ],
        },
    ];

    return (
        <aside
            className={`
                fixed lg:static inset-y-0 start-0 z-30
                w-60 flex-shrink-0 bg-white dark:bg-gray-800
                border-e border-gray-200 dark:border-gray-700
                transform transition-transform duration-200 ease-in-out
                ${isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
            `}
        >
            <div className="flex flex-col h-full pt-4 overflow-y-auto">
                {/* Mobile close button */}
                <button
                    type="button"
                    className="lg:hidden absolute top-3 end-3 p-1 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700"
                    onClick={onClose}
                    aria-label="Close menu"
                >
                    <CloseIcon className="h-5 w-5" />
                </button>

                {/* Navigation */}
                <nav className="flex-1 px-3 space-y-6">
                    {navItems.map((group) => (
                        <div key={group.section}>
                            <h3
                                className={`
                                    px-3 mb-2 text-xs font-semibold uppercase tracking-wider
                                    ${group.disabled
                                        ? 'text-gray-300 dark:text-gray-600'
                                        : 'text-gray-500 dark:text-gray-400'
                                    }
                                `}
                            >
                                {group.section}
                                {group.disabled && (
                                    <span className="ms-2 text-[10px] font-normal normal-case">(Later)</span>
                                )}
                            </h3>
                            <ul className="space-y-1">
                                {group.items.map((item) => {
                                    const Icon = item.icon;
                                    return (
                                        <li key={item.label}>
                                            <a
                                                href={item.href}
                                                onClick={(e) => {
                                                    if (item.disabled) {
                                                        e.preventDefault();
                                                        return;
                                                    }
                                                    // Close mobile sidebar on navigation
                                                    onClose?.();
                                                }}
                                                className={`
                                                    flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium
                                                    transition-colors duration-150
                                                    ${item.disabled
                                                        ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed'
                                                        : item.active
                                                            ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400'
                                                            : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700'
                                                    }
                                                `}
                                                aria-current={item.active ? 'page' : undefined}
                                            >
                                                <Icon className="h-5 w-5 flex-shrink-0" />
                                                {item.label}
                                            </a>
                                        </li>
                                    );
                                })}
                            </ul>
                        </div>
                    ))}
                </nav>

                {/* Footer */}
                <div className="p-4 border-t border-gray-200 dark:border-gray-700">
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                        DOFS Dashboard v1.0
                    </p>
                </div>
            </div>
        </aside>
    );
}

// Icon components
function CloseIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    );
}

function HomeIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
    );
}

function UserIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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

function ClipboardIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
    );
}

function TasksIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}

function ChartIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
    );
}

function ShoppingIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    );
}
