import React from 'react';
import { useMyHr } from '../../hooks/useMyHr';

/**
 * My HR panel component showing user's own HR status
 */
export default function MyHrPanel() {
    const { data, loading, error } = useMyHr();

    return (
        <div className="dashboard-card">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                My HR
            </h2>

            {/* Loading state */}
            {loading && (
                <div className="space-y-6">
                    {[1, 2, 3].map((i) => (
                        <div key={i} className="space-y-2">
                            <div className="skeleton h-4 w-20" />
                            <div className="skeleton h-6 w-32" />
                        </div>
                    ))}
                </div>
            )}

            {/* Error state */}
            {error && (
                <div className="text-center py-4 text-red-600 dark:text-red-400">
                    Failed to load HR data.
                </div>
            )}

            {/* Data display */}
            {!loading && !error && data && (
                <div className="space-y-6">
                    {/* Today section */}
                    <Section title="Today">
                        <div className="flex items-center justify-between mb-2">
                            <span className={`status-badge status-badge--${data.today?.status || 'absent'}`}>
                                {data.today?.status_label || 'Unknown'}
                            </span>
                        </div>

                        {/* Punch timeline */}
                        {data.today?.punches && data.today.punches.length > 0 && (
                            <div className="mt-3 space-y-2">
                                {data.today.punches.map((punch, index) => (
                                    <div
                                        key={index}
                                        className="flex items-center gap-2 text-sm"
                                    >
                                        <span
                                            className={`
                                                w-2 h-2 rounded-full
                                                ${punch.type === 'in'
                                                    ? 'bg-green-500'
                                                    : 'bg-red-500'
                                                }
                                            `}
                                        />
                                        <span className="text-gray-500 dark:text-gray-400">
                                            {punch.type === 'in' ? 'In' : 'Out'}
                                        </span>
                                        <span className="font-medium text-gray-900 dark:text-white">
                                            {punch.time}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}

                        <a
                            href={data.links?.punch || '/my-attendance'}
                            className="block mt-3 text-sm link"
                        >
                            View full attendance
                        </a>
                    </Section>

                    {/* Leave section */}
                    <Section title="Leave">
                        <div className="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <p className="text-xs text-gray-500 dark:text-gray-400">Annual</p>
                                <p className="text-lg font-semibold text-gray-900 dark:text-white">
                                    {data.leave?.annual_balance || 0} <span className="text-sm font-normal">days</span>
                                </p>
                            </div>
                            <div>
                                <p className="text-xs text-gray-500 dark:text-gray-400">Sick</p>
                                <p className="text-lg font-semibold text-gray-900 dark:text-white">
                                    {data.leave?.sick_balance || 0} <span className="text-sm font-normal">days</span>
                                </p>
                            </div>
                        </div>

                        {data.leave?.next_leave && (
                            <div className="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md text-sm">
                                <p className="text-blue-700 dark:text-blue-400">
                                    Next leave: {formatDateRange(data.leave.next_leave.from, data.leave.next_leave.to)}
                                </p>
                                <p className="text-xs text-blue-600 dark:text-blue-500 mt-0.5">
                                    {data.leave.next_leave.type}
                                </p>
                            </div>
                        )}

                        <a
                            href={data.links?.request_leave || '/leave-request'}
                            className="block mt-3 text-sm link"
                        >
                            Request leave
                        </a>
                    </Section>

                    {/* Loans section */}
                    <Section title="Loans">
                        {data.loans?.has_active && data.loans?.active?.length > 0 ? (
                            <div className="space-y-3">
                                {data.loans.active.map((loan) => (
                                    <div
                                        key={loan.loan_id}
                                        className="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md"
                                    >
                                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                                            {loan.label}
                                        </p>
                                        <div className="flex justify-between mt-1 text-xs">
                                            <span className="text-gray-500 dark:text-gray-400">
                                                Remaining
                                            </span>
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {formatCurrency(loan.remaining_amount)}
                                            </span>
                                        </div>
                                        <div className="flex justify-between text-xs">
                                            <span className="text-gray-500 dark:text-gray-400">
                                                Next installment
                                            </span>
                                            <span className="text-gray-700 dark:text-gray-300">
                                                {loan.next_installment_month}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                No active loans
                            </p>
                        )}

                        <a
                            href={data.links?.request_loan || '/loan-request'}
                            className="block mt-3 text-sm link"
                        >
                            Request loan
                        </a>
                    </Section>
                </div>
            )}
        </div>
    );
}

/**
 * Section component
 */
function Section({ title, children }) {
    return (
        <div className="border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                {title}
            </h3>
            {children}
        </div>
    );
}

/**
 * Format date range
 */
function formatDateRange(from, to) {
    const fromDate = new Date(from);
    const toDate = new Date(to);

    const fromStr = fromDate.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
    });

    if (from === to) {
        return fromStr;
    }

    const toStr = toDate.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
    });

    return `${fromStr} - ${toStr}`;
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0,
    }).format(amount);
}
