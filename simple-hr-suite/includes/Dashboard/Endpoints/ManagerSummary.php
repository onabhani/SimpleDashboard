<?php
/**
 * Manager Summary Endpoint
 *
 * GET /manager/summary - Returns KPI row data
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class ManagerSummary {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/manager/summary', [
            'methods' => 'GET',
            'callback' => [$this, 'get_summary'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'range' => [
                    'default' => 'today',
                    'enum' => ['today', 'week', 'month'],
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
                __('You do not have permission to access this endpoint.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get manager summary data
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_summary(\WP_REST_Request $request): \WP_REST_Response {
        $range = $request->get_param('range');
        $user_id = get_current_user_id();

        // Get team attendance data
        $team_attendance = $this->get_team_attendance($user_id, $range);

        // Get pending HR requests
        $hr_pending = $this->get_hr_pending($user_id);

        // Get user's own HR status
        $my_hr = $this->get_my_hr_status($user_id);

        return new \WP_REST_Response([
            'range' => $range,
            'team_attendance' => $team_attendance,
            'hr_pending' => $hr_pending,
            'my_hr' => $my_hr,
        ], 200);
    }

    /**
     * Get team attendance summary
     *
     * @param int $user_id Manager user ID
     * @param string $range Date range
     * @return array
     */
    private function get_team_attendance(int $user_id, string $range): array {
        // Get team members based on department
        $team_members = $this->get_team_members($user_id);
        $total = count($team_members);

        // For demo/mock purposes, generate realistic data
        // In production, this would query actual attendance records
        $present = (int) floor($total * 0.75);
        $absent = (int) floor($total * 0.1);
        $on_leave = $total - $present - $absent;
        $late = (int) floor($present * 0.2);

        // Apply filter for actual HR system integration
        return apply_filters('sfs_hr_team_attendance', [
            'present' => $present,
            'absent' => $absent,
            'on_leave' => $on_leave,
            'off' => 0,
            'total' => $total,
            'late' => $late,
        ], $user_id, $range);
    }

    /**
     * Get pending HR requests count
     *
     * @param int $user_id Manager user ID
     * @return array
     */
    private function get_hr_pending(int $user_id): array {
        // Apply filter for actual HR system integration
        return apply_filters('sfs_hr_pending_requests_count', [
            'total' => 3,
            'leave' => 2,
            'loan' => 1,
        ], $user_id);
    }

    /**
     * Get user's own HR status
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_my_hr_status(int $user_id): array {
        $now = current_time('H:i');
        $hour = (int) current_time('G');

        // Determine status based on time (mock logic)
        $status = 'present';
        $status_label = 'On duty';
        $first_punch = sprintf('%02d:%02d', max(7, min(9, $hour - rand(0, 2))), rand(0, 59));
        $last_punch = $hour > 12 ? sprintf('%02d:%02d', min($hour, 17), rand(0, 59)) : null;

        // Apply filter for actual HR system integration
        return apply_filters('sfs_hr_my_status', [
            'status' => $status,
            'status_label' => $status_label,
            'first_punch' => $first_punch,
            'last_punch' => $last_punch,
            'has_open_loan' => true,
        ], $user_id);
    }

    /**
     * Get team members for a manager
     *
     * @param int $manager_id Manager user ID
     * @return array
     */
    private function get_team_members(int $manager_id): array {
        // Apply filter for actual HR system integration
        // Default returns mock data with 15 team members
        return apply_filters('sfs_hr_team_members', array_fill(0, 15, ['id' => 0]), $manager_id);
    }
}
