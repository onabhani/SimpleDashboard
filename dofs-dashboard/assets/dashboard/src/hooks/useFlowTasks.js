import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch GravityFlow tasks
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useFlowTasks() {
    const { refreshKey } = useDashboard();

    const fetchFn = useCallback(() => {
        return api.getFlowTasks();
    }, []);

    return useApi(fetchFn, [refreshKey]);
}

export default useFlowTasks;
