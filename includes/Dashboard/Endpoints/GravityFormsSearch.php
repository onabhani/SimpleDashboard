<?php
/**
 * Gravity Forms Search Endpoint
 *
 * Provides search functionality across all Gravity Forms entries.
 *
 * @package DOFS_Theme
 */

namespace SFS_HR\Dashboard\Endpoints;

defined('ABSPATH') || exit;

class GravityFormsSearch {

    /**
     * Register the REST API route
     *
     * @param string $namespace REST namespace
     */
    public function register(string $namespace): void {
        register_rest_route($namespace, '/search/entries', [
            'methods' => 'GET',
            'callback' => [$this, 'search_entries'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'q' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'description' => 'Search query',
                ],
                'form_id' => [
                    'required' => false,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'description' => 'Limit search to specific form',
                ],
                'per_page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 20,
                    'sanitize_callback' => 'absint',
                ],
                'page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
    }

    /**
     * Check if user has permission to search entries
     */
    public function check_permission(): bool {
        return current_user_can('gravityforms_view_entries') ||
               current_user_can('dofs.view_dashboard') ||
               current_user_can('administrator');
    }

    /**
     * Search Gravity Forms entries
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function search_entries(\WP_REST_Request $request) {
        // Check if Gravity Forms is active
        if (!class_exists('GFAPI')) {
            return new \WP_Error(
                'gravity_forms_not_active',
                __('Gravity Forms plugin is not active.', 'dofs-theme'),
                ['status' => 400]
            );
        }

        $query = $request->get_param('q');
        $form_id = $request->get_param('form_id');
        $per_page = min($request->get_param('per_page'), 100); // Max 100
        $page = $request->get_param('page');
        $offset = ($page - 1) * $per_page;

        // Get forms to search
        $forms = $this->get_searchable_forms($form_id);

        if (empty($forms)) {
            return rest_ensure_response([
                'results' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $per_page,
                'total_pages' => 0,
            ]);
        }

        // Search entries across forms
        $all_results = [];
        $total_count = 0;

        foreach ($forms as $form) {
            $form_results = $this->search_form_entries($form, $query);
            $all_results = array_merge($all_results, $form_results);
        }

        // Sort by date (most recent first)
        usort($all_results, function($a, $b) {
            return strtotime($b['date_created']) - strtotime($a['date_created']);
        });

        $total_count = count($all_results);

        // Paginate results
        $paginated_results = array_slice($all_results, $offset, $per_page);

        return rest_ensure_response([
            'results' => $paginated_results,
            'total' => $total_count,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total_count / $per_page),
        ]);
    }

    /**
     * Get forms available for searching
     *
     * @param int|null $form_id Specific form ID or null for all
     * @return array
     */
    private function get_searchable_forms($form_id = null): array {
        if ($form_id) {
            $form = \GFAPI::get_form($form_id);
            return $form ? [$form] : [];
        }

        $forms = \GFAPI::get_forms();
        return is_array($forms) ? $forms : [];
    }

    /**
     * Search entries within a specific form
     *
     * @param array $form Form object
     * @param string $query Search query
     * @return array
     */
    private function search_form_entries(array $form, string $query): array {
        $form_id = $form['id'];
        $results = [];

        // Get searchable fields from form
        $searchable_fields = $this->get_searchable_fields($form);

        if (empty($searchable_fields)) {
            return [];
        }

        // Build search criteria for each field
        $search_criteria = [
            'status' => 'active',
            'field_filters' => [
                'mode' => 'any', // OR logic
            ],
        ];

        foreach ($searchable_fields as $field_id) {
            $search_criteria['field_filters'][] = [
                'key' => $field_id,
                'value' => $query,
                'operator' => 'contains',
            ];
        }

        // Also search in entry ID if query is numeric
        if (is_numeric($query)) {
            $entry_by_id = \GFAPI::get_entry(intval($query));
            if (!is_wp_error($entry_by_id) && $entry_by_id['form_id'] == $form_id) {
                $results[] = $this->format_entry_result($entry_by_id, $form);
            }
        }

        // Get entries matching search
        $entries = \GFAPI::get_entries($form_id, $search_criteria, null, ['offset' => 0, 'page_size' => 200]);

        if (is_array($entries)) {
            foreach ($entries as $entry) {
                $results[] = $this->format_entry_result($entry, $form);
            }
        }

        return $results;
    }

    /**
     * Get searchable field IDs from form
     *
     * @param array $form
     * @return array
     */
    private function get_searchable_fields(array $form): array {
        $searchable_types = [
            'text', 'textarea', 'email', 'phone', 'name', 'address',
            'select', 'radio', 'checkbox', 'number', 'hidden',
        ];

        $fields = [];

        if (!empty($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if (in_array($field->type, $searchable_types)) {
                    $fields[] = $field->id;

                    // For complex fields like name/address, add sub-field IDs
                    if (!empty($field->inputs)) {
                        foreach ($field->inputs as $input) {
                            $fields[] = (string) $input['id'];
                        }
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Format entry for response
     *
     * @param array $entry
     * @param array $form
     * @return array
     */
    private function format_entry_result(array $entry, array $form): array {
        $primary_value = $this->get_entry_primary_value($entry, $form);

        return [
            'entry_id' => $entry['id'],
            'form_id' => $entry['form_id'],
            'form_title' => $form['title'],
            'primary_value' => $primary_value,
            'date_created' => $entry['date_created'],
            'created_by' => $entry['created_by'],
            'status' => $entry['status'],
            'edit_url' => admin_url("admin.php?page=gf_entries&view=entry&id={$entry['form_id']}&lid={$entry['id']}"),
        ];
    }

    /**
     * Get primary display value for entry
     *
     * @param array $entry
     * @param array $form
     * @return string
     */
    private function get_entry_primary_value(array $entry, array $form): string {
        // Try to find a name, email, or title field
        $priority_types = ['name', 'email', 'text', 'textarea'];

        foreach ($priority_types as $type) {
            foreach ($form['fields'] as $field) {
                if ($field->type === $type) {
                    $value = \GFFormsModel::get_lead_field_value($entry, $field);

                    if ($field->type === 'name' && is_array($value)) {
                        $value = trim(implode(' ', array_filter($value)));
                    }

                    if (!empty($value) && is_string($value)) {
                        return wp_trim_words($value, 10, '...');
                    }
                }
            }
        }

        return sprintf(__('Entry #%d', 'dofs-theme'), $entry['id']);
    }
}
