<?php
/**
 * Plugin Name: Simple HR Suite
 * Description: DOFS System Dashboard - Manager-focused HR dashboard with team attendance, leave/loan management, and GravityFlow integration.
 * Version: 1.0.0
 * Author: DOFS
 * Text Domain: simple-hr-suite
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

define('SFS_HR_VERSION', '1.0.0');
define('SFS_HR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SFS_HR_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Initialize the plugin
 */
function sfs_hr_init() {
    // Load Dashboard module
    require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/DashboardModule.php';

    $dashboard = new \SFS_HR\Dashboard\DashboardModule();
    $dashboard->init();
}
add_action('plugins_loaded', 'sfs_hr_init');

/**
 * Activation hook - register capabilities
 */
function sfs_hr_activate() {
    // Register custom capabilities
    $admin = get_role('administrator');
    if ($admin) {
        $admin->add_cap('sfs_hr.view_self');
        $admin->add_cap('sfs_hr.view_team');
        $admin->add_cap('sfs_hr.approve_leave');
        $admin->add_cap('sfs_hr.approve_loan');
        $admin->add_cap('sfs_hr.view_dashboard_manager');
    }

    // Register menu location for dashboard topbar
    register_nav_menu('sfs_dashboard_top', __('Dashboard Top Menu', 'simple-hr-suite'));

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sfs_hr_activate');

/**
 * Deactivation hook
 */
function sfs_hr_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'sfs_hr_deactivate');

/**
 * Register menu location on init
 */
function sfs_hr_register_menus() {
    register_nav_menu('sfs_dashboard_top', __('Dashboard Top Menu', 'simple-hr-suite'));
}
add_action('after_setup_theme', 'sfs_hr_register_menus');
