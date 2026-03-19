import { useState, useEffect, useCallback, useRef } from 'react';

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
    const fetchFnRef = useRef(fetchFn);
    fetchFnRef.current = fetchFn;

    const doFetch = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const result = await fetchFnRef.current();
            setData(result);
        } catch (err) {
            console.error('API fetch error:', err);
            setError(err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        doFetch();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, deps);

    return { data, loading, error, refetch: doFetch };
}

export default useApi;
