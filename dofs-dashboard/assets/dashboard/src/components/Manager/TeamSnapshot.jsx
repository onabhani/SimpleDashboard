import React from 'react';
import { useDashboard } from '../../context/DashboardContext';
import { useTeam } from '../../hooks/useTeam';

/**
 * Team attendance snapshot component
 */
export default function TeamSnapshot() {
    const { statusFilter, setStatusFilter } = useDashboard();
    const { data, loading, error } = useTeam();

    const filters = [
        { value: 'all', label: 'All' },
        { value: 'present', label: 'Present' },
        { value: 'absent', label: 'Absent' },
        { value: 'on_leave', label: 'Leave' },
    ];

    const employees = data?.employees || [];

    return (
        <div className="dashboard-card">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                    Team Snapshot
                </h2>
                <div className="flex flex-wrap gap-2">
                    {filters.map((filter) => (
                        <button
                            key={filter.value}
                            type="button"
                            onClick={() => setStatusFilter(filter.value)}
                            className={`filter-chip ${statusFilter === filter.value ? 'filter-chip--active' : ''}`}
                        >
                            {filter.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Loading state */}
            {loading && (
                <div className="space-y-3">
                    {[1, 2, 3, 4, 5].map((i) => (
                        <div key={i} className="flex items-center gap-4">
                            <div className="skeleton h-10 w-10 rounded-full" />
                            <div className="flex-1 space-y-2">
                                <div className="skeleton h-4 w-32" />
                                <div className="skeleton h-3 w-24" />
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Error state */}
            {error && (
                <div className="empty-state">
                    <p className="text-red-600 dark:text-red-400">
                        Failed to load team data. Please refresh the page.
                    </p>
                </div>
            )}

            {/* Empty state */}
            {!loading && !error && employees.length === 0 && (
                <div className="empty-state">
                    <UsersIcon className="mx-auto h-12 w-12 text-gray-400" />
                    <p className="mt-2 text-sm">No team members found.</p>
                </div>
            )}

            {/* Data table */}
            {!loading && !error && employees.length > 0 && (
                <div className="overflow-x-auto -mx-4 sm:mx-0">
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th className="text-xs">Employee</th>
                                <th className="text-xs">Status</th>
                                <th className="text-xs hidden sm:table-cell">First Punch</th>
                                <th className="text-xs hidden sm:table-cell">Last Punch</th>
                                <th className="text-xs hidden md:table-cell">Late</th>
                            </tr>
                        </thead>
                        <tbody>
                            {employees.map((employee) => (
                                <tr key={employee.employee_id}>
                                    <td>
                                        <div className="flex items-center gap-3">
                                            {employee.avatar ? (
                                                <img
                                                    src={employee.avatar}
                                                    alt=""
                                                    className="h-8 w-8 rounded-full object-cover"
                                                />
                                            ) : (
                                                <div className="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs font-medium">
                                                    {employee.name?.charAt(0) || '?'}
                                                </div>
                                            )}
                                            <div>
                                                <p className="font-medium text-gray-900 dark:text-white">
                                                    {employee.name}
                                                </p>
                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                    {employee.department}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <StatusBadge status={employee.status} label={employee.status_label} />
                                    </td>
                                    <td className="hidden sm:table-cell text-gray-600 dark:text-gray-300">
                                        {employee.first_punch || '-'}
                                    </td>
                                    <td className="hidden sm:table-cell text-gray-600 dark:text-gray-300">
                                        {employee.last_punch || '-'}
                                    </td>
                                    <td className="hidden md:table-cell">
                                        {employee.late_minutes > 0 ? (
                                            <span className="text-orange-600 dark:text-orange-400">
                                                {employee.late_minutes} min
                                            </span>
                                        ) : (
                                            <span className="text-gray-400">-</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

/**
 * Status badge component
 */
function StatusBadge({ status, label }) {
    return (
        <span className={`status-badge status-badge--${status}`}>
            {label}
        </span>
    );
}

function UsersIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );
}
