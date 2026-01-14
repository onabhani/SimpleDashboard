<?php
/**
 * Dashboard Shortcode Handler
 *
 * Handles [sfs_hr_dashboard] shortcode rendering and asset enqueuing.
 */

namespace SFS_HR\Dashboard;

defined('ABSPATH') || exit;

class Shortcode {

    /**
     * Initialize shortcode
     */
    public function init(): void {
        add_shortcode('sfs_hr_dashboard', [$this, 'render']);
    }

    /**
     * Render the dashboard shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts = []): string {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url(get_permalink()));
            exit;
        }

        // Check capability
        if (!current_user_can('sfs_hr.view_dashboard_manager')) {
            return $this->render_no_access();
        }

        // Enqueue assets
        $this->enqueue_assets();

        // Output root element
        return '<div id="sfs-hr-dashboard-root" class="sfs-dashboard"></div>';
    }

    /**
     * Render no access message
     *
     * @return string HTML output
     */
    private function render_no_access(): string {
        return '<div class="sfs-dashboard-no-access" style="padding: 40px; text-align: center;">
            <h2>' . esc_html__('Access Denied', 'simple-hr-suite') . '</h2>
            <p>' . esc_html__('You do not have permission to access the manager dashboard.', 'simple-hr-suite') . '</p>
        </div>';
    }

    /**
     * Enqueue dashboard assets
     */
    private function enqueue_assets(): void {
        $user = wp_get_current_user();
        $build_url = SFS_HR_PLUGIN_URL . 'assets/dashboard/build/';
        $build_path = SFS_HR_PLUGIN_DIR . 'assets/dashboard/build/';

        // Enqueue styles
        wp_enqueue_style(
            'sfs-hr-dashboard',
            $build_url . 'dashboard.css',
            [],
            file_exists($build_path . 'dashboard.css') ? filemtime($build_path . 'dashboard.css') : SFS_HR_VERSION
        );

        // Enqueue scripts
        wp_enqueue_script(
            'sfs-hr-dashboard',
            $build_url . 'dashboard.js',
            [],
            file_exists($build_path . 'dashboard.js') ? filemtime($build_path . 'dashboard.js') : SFS_HR_VERSION,
            true
        );

        // Get user avatar - try employee photo first, fallback to WP avatar
        $avatar_url = $this->get_user_avatar($user->ID);

        // Get menu items
        $menu_items = $this->get_menu_items();

        // Localize script with boot data
        wp_localize_script('sfs-hr-dashboard', 'SFS_HR_DASHBOARD_BOOT', [
            'rest_url' => rest_url('sfs-hr/v1/dashboard/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'user' => [
                'id' => $user->ID,
                'name' => $user->display_name,
                'avatar' => $avatar_url,
                'roles' => $user->roles,
                'is_manager' => current_user_can('sfs_hr.view_dashboard_manager'),
            ],
            'menu_items' => $menu_items,
        ]);
    }

    /**
     * Get user avatar URL
     *
     * @param int $user_id User ID
     * @return string Avatar URL
     */
    private function get_user_avatar(int $user_id): string {
        // Try to get employee photo if available (hook for HR system integration)
        $employee_photo = apply_filters('sfs_hr_employee_photo', null, $user_id);

        if ($employee_photo) {
            return $employee_photo;
        }

        // Fallback to WordPress avatar
        return get_avatar_url($user_id, ['size' => 96]);
    }

    /**
     * Get menu items from WP menu location
     *
     * @return array Menu items
     */
    private function get_menu_items(): array {
        $menu_items = [];
        $locations = get_nav_menu_locations();

        if (isset($locations['sfs_dashboard_top'])) {
            $menu = wp_get_nav_menu_object($locations['sfs_dashboard_top']);

            if ($menu) {
                $items = wp_get_nav_menu_items($menu->term_id);

                if ($items) {
                    foreach ($items as $item) {
                        $menu_items[] = [
                            'id' => $item->ID,
                            'title' => $item->title,
                            'url' => $item->url,
                            'target' => $item->target,
                        ];
                    }
                }
            }
        }

        return $menu_items;
    }
}
