<?php
/**
 * Plugin Name: DOFS System Dashboard
 * Description: Comprehensive business dashboard with Sales, Orders, HR, Reports, Inventory, and Quick Access modules.
 * Version: 1.0.0
 * Author: DOFS
 * Text Domain: dofs-dashboard
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

define('DOFS_VERSION', '1.0.0');
define('DOFS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DOFS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Keep legacy constants for backwards compatibility
define('SFS_HR_VERSION', DOFS_VERSION);
define('SFS_HR_PLUGIN_DIR', DOFS_PLUGIN_DIR);
define('SFS_HR_PLUGIN_URL', DOFS_PLUGIN_URL);

/**
 * Initialize the plugin
 */
function dofs_init() {
    // Load Dashboard module
    require_once DOFS_PLUGIN_DIR . 'includes/Dashboard/DashboardModule.php';

    $dashboard = new \SFS_HR\Dashboard\DashboardModule();
    $dashboard->init();
}
add_action('plugins_loaded', 'dofs_init');

/**
 * Activation hook - register capabilities
 */
function dofs_activate() {
    // Register custom capabilities
    $admin = get_role('administrator');
    if ($admin) {
        // Dashboard capabilities
        $admin->add_cap('dofs.view_dashboard');
        $admin->add_cap('dofs.view_sales');
        $admin->add_cap('dofs.view_orders');
        $admin->add_cap('dofs.view_reports');
        $admin->add_cap('dofs.view_inventory');

        // HR capabilities (legacy)
        $admin->add_cap('sfs_hr.view_self');
        $admin->add_cap('sfs_hr.view_team');
        $admin->add_cap('sfs_hr.approve_leave');
        $admin->add_cap('sfs_hr.approve_loan');
        $admin->add_cap('sfs_hr.view_dashboard_manager');
    }

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'dofs_activate');

/**
 * Deactivation hook
 */
function dofs_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'dofs_deactivate');

/**
 * Register menu locations
 */
function dofs_register_menus() {
    register_nav_menus([
        'dofs_dashboard_top' => __('Dashboard Top Menu', 'dofs-dashboard'),
        'dofs_dashboard_sidebar' => __('Dashboard Sidebar Menu', 'dofs-dashboard'),
        // Legacy menu locations
        'sfs_dashboard_top' => __('Dashboard Top Menu (Legacy)', 'dofs-dashboard'),
        'sfs_dashboard_sidebar' => __('Dashboard Sidebar Menu (Legacy)', 'dofs-dashboard'),
    ]);
}
add_action('after_setup_theme', 'dofs_register_menus');
