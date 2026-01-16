import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch user's own HR status
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useMyHr() {
    const { refreshKey } = useDashboard();

    const fetchFn = useCallback(() => {
        return api.getMyHrStatus();
    }, []);

    return useApi(fetchFn, [refreshKey]);
}

export default useMyHr;
