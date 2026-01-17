import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch team data
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useTeam() {
    const { dateRange, scope, statusFilter, refreshKey } = useDashboard();

    const fetchFn = useCallback(() => {
        return api.getManagerTeam({
            range: dateRange,
            scope: scope,
            status: statusFilter,
        });
    }, [dateRange, scope, statusFilter]);

    return useApi(fetchFn, [dateRange, scope, statusFilter, refreshKey]);
}

export default useTeam;
