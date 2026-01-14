/**
 * API Client for dashboard REST endpoints
 */

// Get boot data from WordPress
const boot = window.SFS_HR_DASHBOARD_BOOT || {
    rest_url: '/wp-json/sfs-hr/v1/dashboard/',
    nonce: '',
    user: {
        id: 0,
        name: 'Guest',
        avatar: null,
        roles: [],
        is_manager: false,
    },
    menu_items: [],
};

/**
 * Get boot data
 * @returns {Object} Boot configuration
 */
export function getBootData() {
    return boot;
}

/**
 * Make an API request to the dashboard endpoints
 * @param {string} endpoint - API endpoint path
 * @param {Object} params - Query parameters
 * @param {Object} options - Fetch options
 * @returns {Promise<Object>} API response data
 */
export async function apiFetch(endpoint, params = {}, options = {}) {
    const url = new URL(boot.rest_url + endpoint, window.location.origin);

    // Add query parameters
    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            url.searchParams.set(key, value);
        }
    });

    const response = await fetch(url, {
        method: 'GET',
        ...options,
        headers: {
            'X-WP-Nonce': boot.nonce,
            'Content-Type': 'application/json',
            ...options.headers,
        },
    });

    if (!response.ok) {
        const error = new Error(`API error: ${response.status}`);
        error.status = response.status;

        try {
            const data = await response.json();
            error.message = data.message || error.message;
            error.code = data.code;
        } catch {
            // Ignore JSON parse errors
        }

        throw error;
    }

    return response.json();
}

/**
 * API endpoints
 */
export const api = {
    /**
     * Get manager summary data
     * @param {Object} params
     * @param {string} params.range - Date range (today, week, month)
     */
    getManagerSummary: (params = {}) => apiFetch('manager/summary', params),

    /**
     * Get team data
     * @param {Object} params
     * @param {string} params.range - Date range
     * @param {string} params.scope - Team scope
     * @param {string} params.status - Status filter
     */
    getManagerTeam: (params = {}) => apiFetch('manager/team', params),

    /**
     * Get user's HR status
     */
    getMyHrStatus: () => apiFetch('me/hr-status'),

    /**
     * Get HR requests for approval
     * @param {Object} params
     * @param {string} params.type - Request type (all, leave, loan)
     * @param {string} params.status - Status filter
     * @param {number} params.page - Page number
     * @param {number} params.per_page - Items per page
     */
    getHrRequests: (params = {}) => apiFetch('manager/hr-requests', params),

    /**
     * Get flow tasks
     */
    getFlowTasks: () => apiFetch('me/flow-tasks'),
};
