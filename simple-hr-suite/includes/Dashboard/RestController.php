<?php
/**
 * REST Controller
 *
 * Registers all REST API routes for the dashboard.
 */

namespace SFS_HR\Dashboard;

defined('ABSPATH') || exit;

class RestController {

    /**
     * REST namespace
     */
    private const NAMESPACE = 'sfs-hr/v1/dashboard';

    /**
     * Initialize REST routes
     */
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register all REST routes
     */
    public function register_routes(): void {
        // Manager Summary endpoint
        $summary = new Endpoints\ManagerSummary();
        $summary->register(self::NAMESPACE);

        // Manager Team endpoint
        $team = new Endpoints\ManagerTeam();
        $team->register(self::NAMESPACE);

        // My HR Status endpoint
        $my_hr = new Endpoints\MyHrStatus();
        $my_hr->register(self::NAMESPACE);

        // HR Requests endpoint
        $requests = new Endpoints\HrRequests();
        $requests->register(self::NAMESPACE);

        // Flow Tasks endpoint
        $tasks = new Endpoints\FlowTasks();
        $tasks->register(self::NAMESPACE);

        // Sales Overview endpoint
        $sales = new Endpoints\SalesOverview();
        $sales->register(self::NAMESPACE);

        // Reports Overview endpoint
        $reports = new Endpoints\ReportsOverview();
        $reports->register(self::NAMESPACE);
    }
}
