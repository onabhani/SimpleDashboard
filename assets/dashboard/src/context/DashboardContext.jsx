import React, { createContext, useContext, useState, useCallback } from 'react';

/**
 * Dashboard context for managing global state
 */
const DashboardContext = createContext(null);

/**
 * Dashboard provider component
 */
export function DashboardProvider({ children }) {
    // Date range filter (today, week, month)
    const [dateRange, setDateRange] = useState('today');

    // Team scope filter
    const [scope, setScope] = useState('my_team');

    // Team status filter
    const [statusFilter, setStatusFilter] = useState('all');

    // HR requests type filter
    const [hrRequestType, setHrRequestType] = useState('all');

    // Refresh triggers for data refetching
    const [refreshKey, setRefreshKey] = useState(0);

    // Trigger a data refresh
    const refresh = useCallback(() => {
        setRefreshKey((prev) => prev + 1);
    }, []);

    const value = {
        dateRange,
        setDateRange,
        scope,
        setScope,
        statusFilter,
        setStatusFilter,
        hrRequestType,
        setHrRequestType,
        refreshKey,
        refresh,
    };

    return (
        <DashboardContext.Provider value={value}>
            {children}
        </DashboardContext.Provider>
    );
}

/**
 * Hook to access dashboard context
 */
export function useDashboard() {
    const context = useContext(DashboardContext);

    if (!context) {
        throw new Error('useDashboard must be used within a DashboardProvider');
    }

    return context;
}

export default DashboardContext;
