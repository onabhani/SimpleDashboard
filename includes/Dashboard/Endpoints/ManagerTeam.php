<?php
/**
 * Manager Team Endpoint
 *
 * GET /manager/team - Returns team attendance snapshot
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class ManagerTeam {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/manager/team', [
            'methods' => 'GET',
            'callback' => [$this, 'get_team'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'range' => [
                    'default' => 'today',
                    'enum' => ['today', 'week', 'month'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'scope' => [
                    'default' => 'my_team',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'status' => [
                    'default' => 'all',
                    'enum' => ['all', 'present', 'absent', 'on_leave'],
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

        if (!current_user_can('sfs_hr.view_team')) {
            return new \WP_Error(
                'rest_forbidden',
                __('You do not have permission to view team data.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get team data
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_team(\WP_REST_Request $request): \WP_REST_Response {
        $range = $request->get_param('range');
        $scope = $request->get_param('scope');
        $status_filter = $request->get_param('status');
        $user_id = get_current_user_id();

        // Get team members
        $employees = $this->get_team_employees($user_id, $range, $status_filter);

        return new \WP_REST_Response([
            'range' => $range,
            'scope' => $scope,
            'employees' => $employees,
        ], 200);
    }

    /**
     * Get team employees with attendance status
     *
     * @param int $user_id Manager user ID
     * @param string $range Date range
     * @param string $status_filter Status filter
     * @return array
     */
    private function get_team_employees(int $user_id, string $range, string $status_filter): array {
        // Mock employee data - in production this would query actual HR records
        $mock_employees = [
            [
                'employee_id' => 101,
                'name' => 'Ahmed Ali',
                'avatar' => null,
                'status' => 'present',
                'status_label' => 'Present',
                'first_punch' => '08:07',
                'last_punch' => '12:30',
                'late_minutes' => 5,
                'department' => 'Sales - Riyadh',
            ],
            [
                'employee_id' => 102,
                'name' => 'Sara Mohammed',
                'avatar' => null,
                'status' => 'on_leave',
                'status_label' => 'On leave (Annual)',
                'first_punch' => null,
                'last_punch' => null,
                'late_minutes' => 0,
                'department' => 'Sales - Riyadh',
            ],
            [
                'employee_id' => 103,
                'name' => 'Yousef Ibrahim',
                'avatar' => null,
                'status' => 'present',
                'status_label' => 'Present',
                'first_punch' => '07:55',
                'last_punch' => '12:45',
                'late_minutes' => 0,
                'department' => 'Sales - Riyadh',
            ],
            [
                'employee_id' => 104,
                'name' => 'Fatima Hassan',
                'avatar' => null,
                'status' => 'present',
                'status_label' => 'Present',
                'first_punch' => '08:15',
                'last_punch' => '12:20',
                'late_minutes' => 15,
                'department' => 'Operations',
            ],
            [
                'employee_id' => 105,
                'name' => 'Omar Khalid',
                'avatar' => null,
                'status' => 'absent',
                'status_label' => 'Absent',
                'first_punch' => null,
                'last_punch' => null,
                'late_minutes' => 0,
                'department' => 'Operations',
            ],
            [
                'employee_id' => 106,
                'name' => 'Layla Ahmed',
                'avatar' => null,
                'status' => 'present',
                'status_label' => 'Present',
                'first_punch' => '08:02',
                'last_punch' => null,
                'late_minutes' => 2,
                'department' => 'HR',
            ],
            [
                'employee_id' => 107,
                'name' => 'Khalid Nasser',
                'avatar' => null,
                'status' => 'present',
                'status_label' => 'Present',
                'first_punch' => '07:45',
                'last_punch' => '12:30',
                'late_minutes' => 0,
                'department' => 'Finance',
            ],
            [
                'employee_id' => 108,
                'name' => 'Nora Abdullah',
                'avatar' => null,
                'status' => 'on_leave',
                'status_label' => 'On leave (Sick)',
                'first_punch' => null,
                'last_punch' => null,
                'late_minutes' => 0,
                'department' => 'Sales - Riyadh',
            ],
        ];

        // Apply filter for actual HR system integration
        $employees = apply_filters('sfs_hr_team_employees', $mock_employees, $user_id, $range);

        // Filter by status if needed
        if ($status_filter !== 'all') {
            $employees = array_values(array_filter($employees, function($emp) use ($status_filter) {
                return $emp['status'] === $status_filter;
            }));
        }

        return $employees;
    }
}
