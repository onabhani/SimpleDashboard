<?php
/**
 * Reports Overview Endpoint
 *
 * GET /reports/overview - Returns reports and analytics data
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class ReportsOverview {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/reports/overview', [
            'methods' => 'GET',
            'callback' => [$this, 'get_overview'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
    }

    /**
     * Check if user has permission
     *
     * @return bool|\WP_Error
     */
    public function check_permission() {
        if (!is_user_logged_in()) {
            return new \WP_Error(
                'rest_not_logged_in',
                __('You must be logged in to access this endpoint.', 'simple-hr-suite'),
                ['status' => 401]
            );
        }

        if (!current_user_can('sfs_hr.view_dashboard_manager')) {
            return new \WP_Error(
                'rest_forbidden',
                __('You do not have permission to view reports.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get reports overview data
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_overview(\WP_REST_Request $request): \WP_REST_Response {
        // Get revenue trend data
        $revenue_trend = $this->get_revenue_trend();

        // Get sales by category
        $sales_by_category = $this->get_sales_by_category();

        // Get top products
        $top_products = $this->get_top_products();

        // Get recent reports
        $recent_reports = $this->get_recent_reports();

        // Get quick stats
        $quick_stats = $this->get_quick_stats();

        return new \WP_REST_Response([
            'revenueTrend' => $revenue_trend,
            'salesByCategory' => $sales_by_category,
            'topProducts' => $top_products,
            'recentReports' => $recent_reports,
            'quickStats' => $quick_stats,
        ], 200);
    }

    /**
     * Get revenue trend data
     *
     * @return array
     */
    private function get_revenue_trend(): array {
        return apply_filters('sfs_hr_revenue_trend', [
            'data' => [85000, 92000, 78000, 105000, 115000, 98000, 125000],
            'labels' => ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
        ]);
    }

    /**
     * Get sales by category
     *
     * @return array
     */
    private function get_sales_by_category(): array {
        return apply_filters('sfs_hr_sales_by_category', [
            'data' => [35, 25, 20, 12, 8],
            'labels' => ['Electronics', 'Fashion', 'Home', 'Sports', 'Other'],
        ]);
    }

    /**
     * Get top products
     *
     * @return array
     */
    private function get_top_products(): array {
        return apply_filters('sfs_hr_top_products', [
            [
                'name' => 'iPhone 15 Pro Max',
                'sales' => 245,
                'revenue' => 489500,
                'trend' => 'up',
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'sales' => 189,
                'revenue' => 283500,
                'trend' => 'up',
            ],
            [
                'name' => 'MacBook Pro 14"',
                'sales' => 92,
                'revenue' => 276000,
                'trend' => 'down',
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'sales' => 156,
                'revenue' => 187200,
                'trend' => 'up',
            ],
            [
                'name' => 'AirPods Pro 2',
                'sales' => 312,
                'revenue' => 93600,
                'trend' => 'up',
            ],
        ]);
    }

    /**
     * Get recent reports
     *
     * @return array
     */
    private function get_recent_reports(): array {
        return apply_filters('sfs_hr_recent_reports', [
            [
                'id' => 1,
                'name' => 'Monthly Sales Report',
                'type' => 'Sales',
                'date' => current_time('Y-m-d'),
                'status' => 'ready',
            ],
            [
                'id' => 2,
                'name' => 'Q4 Performance Analysis',
                'type' => 'Analytics',
                'date' => date('Y-m-d', strtotime('-2 days')),
                'status' => 'ready',
            ],
            [
                'id' => 3,
                'name' => 'Inventory Turnover Report',
                'type' => 'Inventory',
                'date' => date('Y-m-d', strtotime('-4 days')),
                'status' => 'ready',
            ],
            [
                'id' => 4,
                'name' => 'Customer Acquisition Report',
                'type' => 'Marketing',
                'date' => date('Y-m-d', strtotime('-6 days')),
                'status' => 'processing',
            ],
        ]);
    }

    /**
     * Get quick stats
     *
     * @return array
     */
    private function get_quick_stats(): array {
        return apply_filters('sfs_hr_quick_stats', [
            'totalCustomers' => [
                'value' => 5248,
                'change' => '+12%',
                'trend' => 'up',
            ],
            'activeProducts' => [
                'value' => 1234,
                'change' => '+5%',
                'trend' => 'up',
            ],
            'pendingShipments' => [
                'value' => 89,
                'change' => '-8%',
                'trend' => 'down',
            ],
            'returnsRate' => [
                'value' => 2.4,
                'change' => '-0.3%',
                'trend' => 'down',
            ],
        ]);
    }
}
