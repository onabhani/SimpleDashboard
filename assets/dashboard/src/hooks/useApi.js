import { useState, useEffect, useCallback } from 'react';

/**
 * Generic API fetch hook with loading and error states
 *
 * @param {Function} fetchFn - Async function that returns data
 * @param {Array} deps - Dependencies to trigger refetch
 * @returns {Object} { data, loading, error, refetch }
 */
export function useApi(fetchFn, deps = []) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetch = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await fetchFn();
            setData(result);
        } catch (err) {
            console.error('API fetch error:', err);
            setError(err);
        } finally {
            setLoading(false);
        }
    }, [fetchFn]);

    useEffect(() => {
        fetch();
    }, [...deps, fetch]);

    const refetch = useCallback(() => {
        fetch();
    }, [fetch]);

    return { data, loading, error, refetch };
}

export default useApi;
