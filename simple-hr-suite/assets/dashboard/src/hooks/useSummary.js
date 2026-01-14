import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch manager summary data
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useSummary() {
    const { dateRange, refreshKey } = useDashboard();

    const fetchFn = useCallback(() => {
        return api.getManagerSummary({ range: dateRange });
    }, [dateRange]);

    return useApi(fetchFn, [dateRange, refreshKey]);
}

export default useSummary;
