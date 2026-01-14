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
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Shortcode.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/RestController.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/ManagerSummary.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/ManagerTeam.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/MyHrStatus.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/HrRequests.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/FlowTasks.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/SalesOverview.php';
        require_once SFS_HR_PLUGIN_DIR . 'includes/Dashboard/Endpoints/ReportsOverview.php';
    }
}
