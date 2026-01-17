<?php
/**
 * My HR Status Endpoint
 *
 * GET /me/hr-status - Returns current user's HR status
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class MyHrStatus {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/me/hr-status', [
            'methods' => 'GET',
            'callback' => [$this, 'get_status'],
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

        if (!current_user_can('sfs_hr.view_self')) {
            return new \WP_Error(
                'rest_forbidden',
                __('You do not have permission to view your HR status.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get user's HR status
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_status(\WP_REST_Request $request): \WP_REST_Response {
        $user_id = get_current_user_id();

        // Get today's attendance
        $today = $this->get_today_status($user_id);

        // Get leave information
        $leave = $this->get_leave_info($user_id);

        // Get loan information
        $loans = $this->get_loan_info($user_id);

        // Get action links
        $links = $this->get_links();

        return new \WP_REST_Response([
            'today' => $today,
            'leave' => $leave,
            'loans' => $loans,
            'links' => $links,
        ], 200);
    }

    /**
     * Get today's attendance status
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_today_status(int $user_id): array {
        // Mock data - in production this would query actual attendance records
        $hour = (int) current_time('G');

        $punches = [
            ['type' => 'in', 'time' => '08:05'],
        ];

        if ($hour > 12) {
            $punches[] = ['type' => 'out', 'time' => '12:15'];
        }
        if ($hour > 13) {
            $punches[] = ['type' => 'in', 'time' => '13:00'];
        }
        if ($hour > 17) {
            $punches[] = ['type' => 'out', 'time' => '17:05'];
        }

        $last_punch = end($punches);

        return apply_filters('sfs_hr_my_today_status', [
            'status' => 'present',
            'status_label' => 'On duty',
            'first_punch' => '08:05',
            'last_punch' => $last_punch['time'],
            'punches' => $punches,
        ], $user_id);
    }

    /**
     * Get leave information
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_leave_info(int $user_id): array {
        return apply_filters('sfs_hr_my_leave_info', [
            'annual_balance' => 12,
            'sick_balance' => 5,
            'next_leave' => [
                'from' => '2026-01-20',
                'to' => '2026-01-22',
                'type' => 'annual',
            ],
        ], $user_id);
    }

    /**
     * Get loan information
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_loan_info(int $user_id): array {
        return apply_filters('sfs_hr_my_loan_info', [
            'has_active' => true,
            'active' => [
                [
                    'loan_id' => 55,
                    'label' => 'Cash advance - Oct',
                    'remaining_amount' => 2500,
                    'next_installment_month' => '2026-02',
                ],
            ],
        ], $user_id);
    }

    /**
     * Get action links
     *
     * @return array
     */
    private function get_links(): array {
        return apply_filters('sfs_hr_action_links', [
            'punch' => '/my-attendance',
            'request_leave' => '/leave-request',
            'request_loan' => '/loan-request',
        ]);
    }
}
