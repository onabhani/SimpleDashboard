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

        // Get menu items for topbar and sidebar
        $topbar_menu = $this->get_menu_items('sfs_dashboard_top');
        $sidebar_menu = $this->get_sidebar_menu();

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
            'menu_items' => $topbar_menu,
            'sidebar_menu' => $sidebar_menu,
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
     * @param string $location Menu location
     * @return array Menu items
     */
    private function get_menu_items(string $location): array {
        $menu_items = [];
        $locations = get_nav_menu_locations();

        if (isset($locations[$location])) {
            $menu = wp_get_nav_menu_object($locations[$location]);

            if ($menu) {
                $items = wp_get_nav_menu_items($menu->term_id);

                if ($items) {
                    foreach ($items as $item) {
                        $menu_items[] = [
                            'id' => $item->ID,
                            'title' => $item->title,
                            'url' => $item->url,
                            'target' => $item->target,
                            'parent' => (int) $item->menu_item_parent,
                            'classes' => implode(' ', $item->classes),
                        ];
                    }
                }
            }
        }

        return $menu_items;
    }

    /**
     * Get sidebar menu with hierarchical structure
     *
     * @return array Sidebar menu sections with items
     */
    private function get_sidebar_menu(): array {
        $locations = get_nav_menu_locations();

        // Check if sidebar menu is configured
        if (!isset($locations['sfs_dashboard_sidebar'])) {
            // Return default menu if no WP menu is set
            return $this->get_default_sidebar_menu();
        }

        $menu = wp_get_nav_menu_object($locations['sfs_dashboard_sidebar']);

        if (!$menu) {
            return $this->get_default_sidebar_menu();
        }

        $items = wp_get_nav_menu_items($menu->term_id);

        if (!$items || empty($items)) {
            return $this->get_default_sidebar_menu();
        }

        // Build hierarchical menu (top-level items are sections, children are items)
        $sections = [];
        $children = [];

        // First pass: separate parents and children
        foreach ($items as $item) {
            $menu_item = [
                'id' => $item->ID,
                'title' => $item->title,
                'url' => $item->url,
                'target' => $item->target,
                'icon' => $this->get_menu_item_icon($item),
                'classes' => $item->classes,
            ];

            if ((int) $item->menu_item_parent === 0) {
                // Top-level item = section header
                $sections[$item->ID] = [
                    'section' => $item->title,
                    'items' => [],
                ];
            } else {
                // Child item
                $children[$item->menu_item_parent][] = $menu_item;
            }
        }

        // Second pass: attach children to parents
        foreach ($children as $parent_id => $child_items) {
            if (isset($sections[$parent_id])) {
                $sections[$parent_id]['items'] = $child_items;
            }
        }

        // Convert to indexed array and filter empty sections
        $result = array_values(array_filter($sections, function($section) {
            return !empty($section['items']);
        }));

        return !empty($result) ? $result : $this->get_default_sidebar_menu();
    }

    /**
     * Extract icon from menu item classes
     * Supports: icon-{name} class format
     *
     * @param \WP_Post $item Menu item
     * @return string|null Icon name
     */
    private function get_menu_item_icon($item): ?string {
        if (empty($item->classes)) {
            return null;
        }

        foreach ($item->classes as $class) {
            if (strpos($class, 'icon-') === 0) {
                return str_replace('icon-', '', $class);
            }
        }

        return null;
    }

    /**
     * Get default sidebar menu when no WP menu is configured
     *
     * @return array Default menu structure
     */
    private function get_default_sidebar_menu(): array {
        return [
            [
                'section' => 'MAIN',
                'items' => [
                    ['id' => 1, 'title' => 'Dashboard', 'url' => '#', 'icon' => 'home', 'active' => true],
                    ['id' => 2, 'title' => 'Quick Access', 'url' => '#quick-access', 'icon' => 'grid'],
                ],
            ],
            [
                'section' => 'SALES & ORDERS',
                'items' => [
                    ['id' => 3, 'title' => 'Sales Overview', 'url' => '#sales-section', 'icon' => 'chart'],
                    ['id' => 4, 'title' => 'Orders', 'url' => '#orders-section', 'icon' => 'cart'],
                    ['id' => 5, 'title' => 'Customers', 'url' => '#customers-section', 'icon' => 'users'],
                ],
            ],
            [
                'section' => 'INVENTORY',
                'items' => [
                    ['id' => 6, 'title' => 'Products', 'url' => '#inventory-section', 'icon' => 'cube'],
                    ['id' => 7, 'title' => 'Stock Levels', 'url' => '#stock', 'icon' => 'archive'],
                ],
            ],
            [
                'section' => 'HR',
                'items' => [
                    ['id' => 8, 'title' => 'HR Overview', 'url' => '#hr-section', 'icon' => 'team'],
                    ['id' => 9, 'title' => 'My HR', 'url' => '#my-hr', 'icon' => 'user'],
                    ['id' => 10, 'title' => 'My Team', 'url' => '#team-snapshot', 'icon' => 'users'],
                    ['id' => 11, 'title' => 'HR Actions', 'url' => '#hr-actions', 'icon' => 'clipboard'],
                ],
            ],
            [
                'section' => 'REPORTS & DATA',
                'items' => [
                    ['id' => 12, 'title' => 'Reports', 'url' => '#reports-section', 'icon' => 'document'],
                    ['id' => 13, 'title' => 'Analytics', 'url' => '#analytics', 'icon' => 'trending'],
                ],
            ],
            [
                'section' => 'WORKFLOWS',
                'items' => [
                    ['id' => 14, 'title' => 'My Tasks', 'url' => '#workflows', 'icon' => 'tasks'],
                ],
            ],
            [
                'section' => 'SETTINGS',
                'items' => [
                    ['id' => 15, 'title' => 'Settings', 'url' => '/settings', 'icon' => 'settings'],
                    ['id' => 16, 'title' => 'Help', 'url' => '/help', 'icon' => 'help'],
                ],
            ],
        ];
    }
}
