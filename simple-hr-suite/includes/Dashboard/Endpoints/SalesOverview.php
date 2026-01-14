<?php
/**
 * Sales Overview Endpoint
 *
 * GET /sales/overview - Returns sales statistics and recent orders
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class SalesOverview {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/sales/overview', [
            'methods' => 'GET',
            'callback' => [$this, 'get_overview'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'range' => [
                    'default' => 'month',
                    'enum' => ['today', 'week', 'month', 'quarter', 'year'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
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
                __('You do not have permission to view sales data.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get sales overview data
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_overview(\WP_REST_Request $request): \WP_REST_Response {
        $range = $request->get_param('range');

        // Get sales statistics
        $stats = $this->get_sales_stats($range);

        // Get recent orders
        $recent_orders = $this->get_recent_orders();

        // Get order status summary
        $order_status = $this->get_order_status_summary();

        return new \WP_REST_Response([
            'range' => $range,
            'stats' => $stats,
            'recentOrders' => $recent_orders,
            'orderStatus' => $order_status,
        ], 200);
    }

    /**
     * Get sales statistics
     *
     * @param string $range Date range
     * @return array
     */
    private function get_sales_stats(string $range): array {
        // Apply filter for actual sales system integration
        // Default returns mock data for demonstration
        return apply_filters('sfs_hr_sales_stats', [
            'revenue' => [
                'value' => 125000,
                'change' => 12.5,
                'trend' => 'up',
            ],
            'orders' => [
                'value' => 1248,
                'change' => 8.2,
                'trend' => 'up',
            ],
            'avgOrder' => [
                'value' => 485,
                'change' => -2.1,
                'trend' => 'down',
            ],
            'conversion' => [
                'value' => 3.2,
                'change' => 0.5,
                'trend' => 'up',
            ],
        ], $range);
    }

    /**
     * Get recent orders
     *
     * @return array
     */
    private function get_recent_orders(): array {
        // Apply filter for actual order system integration
        return apply_filters('sfs_hr_recent_orders', [
            [
                'id' => '10234',
                'customer' => 'Ahmed Al-Rashid',
                'items' => 3,
                'amount' => 1250,
                'status' => 'delivered',
            ],
            [
                'id' => '10233',
                'customer' => 'Sara Mohammed',
                'items' => 1,
                'amount' => 450,
                'status' => 'shipped',
            ],
            [
                'id' => '10232',
                'customer' => 'Khalid Ibrahim',
                'items' => 5,
                'amount' => 2100,
                'status' => 'processing',
            ],
            [
                'id' => '10231',
                'customer' => 'Fatima Hassan',
                'items' => 2,
                'amount' => 890,
                'status' => 'pending',
            ],
            [
                'id' => '10230',
                'customer' => 'Omar Nasser',
                'items' => 4,
                'amount' => 1680,
                'status' => 'delivered',
            ],
        ]);
    }

    /**
     * Get order status summary
     *
     * @return array
     */
    private function get_order_status_summary(): array {
        return apply_filters('sfs_hr_order_status_summary', [
            'pending' => 24,
            'processing' => 35,
            'shipped' => 28,
            'delivered' => 85,
            'cancelled' => 8,
            'total' => 180,
        ]);
    }
}
