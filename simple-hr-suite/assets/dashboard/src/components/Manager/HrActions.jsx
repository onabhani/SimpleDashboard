import React, { useState } from 'react';
import { useHrRequests } from '../../hooks/useHrRequests';

/**
 * HR Actions component for pending leave/loan requests
 */
export default function HrActions() {
    const [activeTab, setActiveTab] = useState('leave');
    const { data, loading, error } = useHrRequests();

    const tabs = [
        { id: 'leave', label: 'Leave Requests' },
        { id: 'loan', label: 'Loan Requests' },
    ];

    const leaveRequests = data?.leave || [];
    const loanRequests = data?.loan || [];

    return (
        <div className="dashboard-card">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                    HR Actions
                </h2>
            </div>

            {/* Tabs */}
            <div className="border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav className="flex gap-4" aria-label="HR Actions tabs">
                    {tabs.map((tab) => {
                        const count = tab.id === 'leave' ? leaveRequests.length : loanRequests.length;
                        return (
                            <button
                                key={tab.id}
                                type="button"
                                onClick={() => setActiveTab(tab.id)}
                                className={`tab ${activeTab === tab.id ? 'tab--active' : ''}`}
                            >
                                {tab.label}
                                {count > 0 && (
                                    <span className="ms-2 px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                        {count}
                                    </span>
                                )}
                            </button>
                        );
                    })}
                </nav>
            </div>

            {/* Loading state */}
            {loading && (
                <div className="space-y-3">
                    {[1, 2, 3].map((i) => (
                        <div key={i} className="flex items-center gap-4">
                            <div className="flex-1 space-y-2">
                                <div className="skeleton h-4 w-32" />
                                <div className="skeleton h-3 w-48" />
                            </div>
                            <div className="skeleton h-8 w-16" />
                        </div>
                    ))}
                </div>
            )}

            {/* Error state */}
            {error && (
                <div className="empty-state">
                    <p className="text-red-600 dark:text-red-400">
                        Failed to load HR requests. Please refresh the page.
                    </p>
                </div>
            )}

            {/* Tab content */}
            {!loading && !error && (
                <>
                    {activeTab === 'leave' && (
                        <LeaveRequestsTable requests={leaveRequests} />
                    )}
                    {activeTab === 'loan' && (
                        <LoanRequestsTable requests={loanRequests} />
                    )}
                </>
            )}
        </div>
    );
}

/**
 * Leave requests table
 */
function LeaveRequestsTable({ requests }) {
    if (requests.length === 0) {
        return (
            <div className="empty-state">
                <ClipboardIcon className="mx-auto h-12 w-12 text-gray-400" />
                <p className="mt-2 text-sm">No pending leave requests.</p>
            </div>
        );
    }

    return (
        <div className="overflow-x-auto -mx-4 sm:mx-0">
            <table className="data-table">
                <thead>
                    <tr>
                        <th className="text-xs">Employee</th>
                        <th className="text-xs">Type</th>
                        <th className="text-xs hidden sm:table-cell">Period</th>
                        <th className="text-xs hidden md:table-cell">Submitted</th>
                        <th className="text-xs">Status</th>
                        <th className="text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {requests.map((request) => (
                        <tr key={request.request_id}>
                            <td className="font-medium text-gray-900 dark:text-white">
                                {request.employee_name}
                            </td>
                            <td className="text-gray-600 dark:text-gray-300 capitalize">
                                {request.type}
                            </td>
                            <td className="hidden sm:table-cell text-gray-600 dark:text-gray-300">
                                {formatDateRange(request.from, request.to)}
                                <span className="text-xs text-gray-500 ms-1">
                                    ({request.days} {request.days === 1 ? 'day' : 'days'})
                                </span>
                            </td>
                            <td className="hidden md:table-cell text-gray-500 dark:text-gray-400 text-sm">
                                {formatDateTime(request.submitted_at)}
                            </td>
                            <td>
                                <span className="status-badge status-badge--pending">
                                    {request.status}
                                </span>
                            </td>
                            <td>
                                <a
                                    href={request.manage_url}
                                    className="text-sm link"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

/**
 * Loan requests table
 */
function LoanRequestsTable({ requests }) {
    if (requests.length === 0) {
        return (
            <div className="empty-state">
                <CurrencyIcon className="mx-auto h-12 w-12 text-gray-400" />
                <p className="mt-2 text-sm">No pending loan requests.</p>
            </div>
        );
    }

    return (
        <div className="overflow-x-auto -mx-4 sm:mx-0">
            <table className="data-table">
                <thead>
                    <tr>
                        <th className="text-xs">Employee</th>
                        <th className="text-xs">Amount</th>
                        <th className="text-xs hidden sm:table-cell">Installments</th>
                        <th className="text-xs hidden md:table-cell">Submitted</th>
                        <th className="text-xs">Status</th>
                        <th className="text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {requests.map((request) => (
                        <tr key={request.loan_id}>
                            <td className="font-medium text-gray-900 dark:text-white">
                                {request.employee_name}
                            </td>
                            <td className="text-gray-600 dark:text-gray-300">
                                {formatCurrency(request.amount)}
                            </td>
                            <td className="hidden sm:table-cell text-gray-600 dark:text-gray-300">
                                {request.installments} months
                            </td>
                            <td className="hidden md:table-cell text-gray-500 dark:text-gray-400 text-sm">
                                {formatDateTime(request.submitted_at)}
                            </td>
                            <td>
                                <span className="status-badge status-badge--pending">
                                    {request.status}
                                </span>
                            </td>
                            <td>
                                <a
                                    href={request.manage_url}
                                    className="text-sm link"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

// Helper functions
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

function formatDateTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0,
    }).format(amount);
}

// Icons
function ClipboardIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
    );
}

function CurrencyIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}
