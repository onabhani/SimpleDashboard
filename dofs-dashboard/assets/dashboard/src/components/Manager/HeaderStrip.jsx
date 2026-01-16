import React from 'react';
import { useDashboard } from '../../context/DashboardContext';

/**
 * Header strip with title and date range selector
 */
export default function HeaderStrip() {
    const { dateRange, setDateRange } = useDashboard();

    const ranges = [
        { value: 'today', label: 'Today' },
        { value: 'week', label: 'This Week' },
    ];

    return (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {/* Title */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                    Manager Dashboard
                </h1>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Team status overview
                </p>
            </div>

            {/* Date range selector */}
            <div className="flex items-center gap-2">
                <span className="text-sm text-gray-500 dark:text-gray-400 me-2">
                    Showing:
                </span>
                <div className="inline-flex rounded-lg bg-gray-100 dark:bg-gray-700 p-1">
                    {ranges.map((range) => (
                        <button
                            key={range.value}
                            type="button"
                            onClick={() => setDateRange(range.value)}
                            className={`
                                px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-150
                                ${dateRange === range.value
                                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm'
                                    : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
                                }
                            `}
                        >
                            {range.label}
                        </button>
                    ))}
                </div>
            </div>
        </div>
    );
}
