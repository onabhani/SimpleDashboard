<?php
/**
 * HR Requests Endpoint
 *
 * GET /manager/hr-requests - Returns pending HR actions for manager approval
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class HrRequests {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/manager/hr-requests', [
            'methods' => 'GET',
            'callback' => [$this, 'get_requests'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'type' => [
                    'default' => 'all',
                    'enum' => ['all', 'leave', 'loan'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'status' => [
                    'default' => 'pending',
                    'enum' => ['pending', 'approved', 'rejected'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'page' => [
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'default' => 10,
                    'sanitize_callback' => 'absint',
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

        // User must have either leave or loan approval capability
        if (!current_user_can('sfs_hr.approve_leave') && !current_user_can('sfs_hr.approve_loan')) {
            return new \WP_Error(
                'rest_forbidden',
                __('You do not have permission to view HR requests.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get HR requests
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_requests(\WP_REST_Request $request): \WP_REST_Response {
        $type = $request->get_param('type');
        $status = $request->get_param('status');
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $user_id = get_current_user_id();

        $response = [];

        // Get leave requests if user has permission and type includes leave
        if (($type === 'all' || $type === 'leave') && current_user_can('sfs_hr.approve_leave')) {
            $response['leave'] = $this->get_leave_requests($user_id, $status, $page, $per_page);
        }

        // Get loan requests if user has permission and type includes loan
        if (($type === 'all' || $type === 'loan') && current_user_can('sfs_hr.approve_loan')) {
            $response['loan'] = $this->get_loan_requests($user_id, $status, $page, $per_page);
        }

        return new \WP_REST_Response($response, 200);
    }

    /**
     * Get leave requests
     *
     * @param int $user_id Manager user ID
     * @param string $status Status filter
     * @param int $page Page number
     * @param int $per_page Items per page
     * @return array
     */
    private function get_leave_requests(int $user_id, string $status, int $page, int $per_page): array {
        // Mock data - in production this would query actual leave requests
        $mock_requests = [
            [
                'request_id' => 201,
                'employee_id' => 101,
                'employee_name' => 'Ahmed Ali',
                'type' => 'annual',
                'from' => '2026-01-20',
                'to' => '2026-01-22',
                'days' => 3,
                'submitted_at' => '2026-01-10 09:10',
                'status' => 'pending',
                'manage_url' => '/hr/leave/201',
            ],
            [
                'request_id' => 202,
                'employee_id' => 104,
                'employee_name' => 'Fatima Hassan',
                'type' => 'sick',
                'from' => '2026-01-25',
                'to' => '2026-01-25',
                'days' => 1,
                'submitted_at' => '2026-01-12 14:30',
                'status' => 'pending',
                'manage_url' => '/hr/leave/202',
            ],
        ];

        // Apply filter for actual HR system integration
        return apply_filters('sfs_hr_leave_requests', $mock_requests, $user_id, $status, $page, $per_page);
    }

    /**
     * Get loan requests
     *
     * @param int $user_id Manager user ID
     * @param string $status Status filter
     * @param int $page Page number
     * @param int $per_page Items per page
     * @return array
     */
    private function get_loan_requests(int $user_id, string $status, int $page, int $per_page): array {
        // Mock data - in production this would query actual loan requests
        $mock_requests = [
            [
                'loan_id' => 301,
                'employee_id' => 103,
                'employee_name' => 'Yousef Ibrahim',
                'amount' => 5000,
                'installments' => 5,
                'submitted_at' => '2026-01-08 10:05',
                'status' => 'pending',
                'manage_url' => '/hr/loan/301',
            ],
        ];

        // Apply filter for actual HR system integration
        return apply_filters('sfs_hr_loan_requests', $mock_requests, $user_id, $status, $page, $per_page);
    }
}
