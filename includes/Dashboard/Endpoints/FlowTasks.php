<?php
/**
 * Flow Tasks Endpoint
 *
 * GET /me/flow-tasks - Returns GravityFlow inbox tasks for current user
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class FlowTasks {

    /**
     * Register the route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/me/flow-tasks', [
            'methods' => 'GET',
            'callback' => [$this, 'get_tasks'],
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
                __('You do not have permission to view tasks.', 'simple-hr-suite'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Get user's flow tasks
     *
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response
     */
    public function get_tasks(\WP_REST_Request $request): \WP_REST_Response {
        $user_id = get_current_user_id();

        // Try to get tasks from GravityFlow if available
        $tasks = $this->get_gravityflow_tasks($user_id);

        return new \WP_REST_Response([
            'tasks' => $tasks,
        ], 200);
    }

    /**
     * Get GravityFlow tasks for user
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_gravityflow_tasks(int $user_id): array {
        // Check if GravityFlow is available
        if (class_exists('Gravity_Flow')) {
            return $this->get_real_gravityflow_tasks($user_id);
        }

        // Return mock data for development/demo
        return $this->get_mock_tasks($user_id);
    }

    /**
     * Get real GravityFlow tasks
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_real_gravityflow_tasks(int $user_id): array {
        $tasks = [];

        // Use GravityFlow API to get pending tasks
        // This uses similar logic to GravityView's user filtering
        if (function_exists('gravity_flow')) {
            $api = gravity_flow();
            $forms = \GFAPI::get_forms();

            foreach ($forms as $form) {
                $form_id = $form['id'];

                // Get entries where user has pending steps
                $search_criteria = [
                    'status' => 'active',
                    'field_filters' => [
                        'mode' => 'any',
                    ],
                ];

                $entries = \GFAPI::get_entries($form_id, $search_criteria);

                foreach ($entries as $entry) {
                    $current_step = $api->get_current_step($form, $entry);

                    if ($current_step && $this->user_can_act_on_step($current_step, $user_id)) {
                        $tasks[] = [
                            'id' => $entry['id'],
                            'entry_id' => $entry['id'],
                            'workflow' => $form['title'],
                            'step' => $current_step->get_name(),
                            'submitted_at' => $entry['date_created'],
                            'summary' => $this->get_entry_summary($entry, $form),
                            'url' => admin_url("admin.php?page=gravityflow-inbox&id={$entry['id']}&form_id={$form_id}"),
                        ];
                    }
                }
            }
        }

        return apply_filters('sfs_hr_flow_tasks', $tasks, $user_id);
    }

    /**
     * Check if user can act on a step
     *
     * @param object $step GravityFlow step
     * @param int $user_id User ID
     * @return bool
     */
    private function user_can_act_on_step($step, int $user_id): bool {
        if (method_exists($step, 'is_assignee')) {
            return $step->is_assignee($user_id);
        }

        return false;
    }

    /**
     * Get entry summary
     *
     * @param array $entry GF entry
     * @param array $form GF form
     * @return string
     */
    private function get_entry_summary(array $entry, array $form): string {
        // Try to get a meaningful summary from the entry
        $summary_parts = [];

        foreach ($form['fields'] as $field) {
            if (isset($entry[$field->id]) && !empty($entry[$field->id])) {
                $value = $entry[$field->id];
                if (is_string($value) && strlen($value) < 100) {
                    $summary_parts[] = $value;
                    if (count($summary_parts) >= 2) {
                        break;
                    }
                }
            }
        }

        return implode(' - ', $summary_parts) ?: "Entry #{$entry['id']}";
    }

    /**
     * Get mock tasks for development
     *
     * @param int $user_id User ID
     * @return array
     */
    private function get_mock_tasks(int $user_id): array {
        return apply_filters('sfs_hr_flow_tasks', [
            [
                'id' => 1,
                'entry_id' => 456,
                'workflow' => 'Quality Check',
                'step' => 'Manager Approval',
                'submitted_at' => '2026-01-10 08:55',
                'summary' => 'Order #12345 - Issue with item delivery',
                'url' => '/wp-admin/admin.php?page=gravityflow-inbox&id=456',
            ],
            [
                'id' => 2,
                'entry_id' => 457,
                'workflow' => 'Expense Report',
                'step' => 'Department Review',
                'submitted_at' => '2026-01-12 14:20',
                'summary' => 'Travel expenses - January conference',
                'url' => '/wp-admin/admin.php?page=gravityflow-inbox&id=457',
            ],
            [
                'id' => 3,
                'entry_id' => 458,
                'workflow' => 'Purchase Request',
                'step' => 'Budget Approval',
                'submitted_at' => '2026-01-13 09:15',
                'summary' => 'Office supplies - Q1 order',
                'url' => '/wp-admin/admin.php?page=gravityflow-inbox&id=458',
            ],
        ], $user_id);
    }
}
