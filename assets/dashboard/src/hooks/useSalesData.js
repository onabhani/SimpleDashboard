import { useCallback } from 'react';
import { useApi } from './useApi';
import { api } from '../api/client';
import { useDashboard } from '../context/DashboardContext';

/**
 * Hook to fetch sales data
 *
 * @returns {Object} { data, loading, error, refetch }
 */
export function useSalesData() {
    const { refreshKey } = useDashboard();

    const fetchFn = useCallback(async () => {
        try {
            return await api.getSalesData();
        } catch (error) {
            // Return mock data if API fails (development mode)
            return getMockSalesData();
        }
    }, []);

    return useApi(fetchFn, [refreshKey]);
}

/**
 * Mock sales data for development
 */
function getMockSalesData() {
    return {
        stats: {
            revenue: { value: 125000, change: 12.5, trend: 'up' },
            orders: { value: 1248, change: 8.2, trend: 'up' },
            avgOrder: { value: 485, change: -2.1, trend: 'down' },
            conversion: { value: 3.2, change: 0.5, trend: 'up' },
        },
        recentOrders: [
            { id: '10234', customer: 'Ahmed Al-Rashid', items: 3, amount: 1250, status: 'delivered' },
            { id: '10233', customer: 'Sara Mohammed', items: 1, amount: 450, status: 'shipped' },
            { id: '10232', customer: 'Khalid Ibrahim', items: 5, amount: 2100, status: 'processing' },
            { id: '10231', customer: 'Fatima Hassan', items: 2, amount: 890, status: 'pending' },
            { id: '10230', customer: 'Omar Nasser', items: 4, amount: 1680, status: 'delivered' },
        ],
    };
}

export default useSalesData;
