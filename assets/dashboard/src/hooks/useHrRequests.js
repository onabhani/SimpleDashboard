import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch HR requests for approval
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useHrRequests() {
    const { hrRequestType, refreshKey } = useDashboard();

    const fetchFn = useCallback(() => {
        return api.getHrRequests({
            type: hrRequestType,
            status: 'pending',
        });
    }, [hrRequestType]);

    return useApi(fetchFn, [hrRequestType, refreshKey]);
}

export default useHrRequests;
