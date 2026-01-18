<?php
/**
 * Dashboard Module
 *
 * Main module class for the DOFS Dashboard functionality.
 */

namespace SFS_HR\Dashboard;

defined('ABSPATH') || exit;

class DashboardModule {

    /**
     * Initialize the dashboard module
     */
    public function init(): void {
        // Load dependencies
        $this->load_dependencies();

        // Initialize shortcode
        $shortcode = new Shortcode();
        $shortcode->init();

        // Initialize REST controller
        $rest = new RestController();
        $rest->init();
    }

    /**
     * Load module dependencies
     */
    private function load_dependencies(): void {
        $base_dir = DOFS_THEME_DIR . '/includes/Dashboard/';

        require_once $base_dir . 'Shortcode.php';
        require_once $base_dir . 'RestController.php';
        require_once $base_dir . 'Endpoints/ManagerSummary.php';
        require_once $base_dir . 'Endpoints/ManagerTeam.php';
        require_once $base_dir . 'Endpoints/MyHrStatus.php';
        require_once $base_dir . 'Endpoints/HrRequests.php';
        require_once $base_dir . 'Endpoints/FlowTasks.php';
        require_once $base_dir . 'Endpoints/SalesOverview.php';
        require_once $base_dir . 'Endpoints/ReportsOverview.php';
        require_once $base_dir . 'Endpoints/GravityFormsSearch.php';
    }
}
