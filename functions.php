<?php
/**
 * DOFS Theme Functions
 *
 * @package DOFS_Theme
 */

defined('ABSPATH') || exit;

define('DOFS_THEME_VERSION', '1.0.0');
define('DOFS_THEME_DIR', get_template_directory());
define('DOFS_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function dofs_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 64,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');

    // Register navigation menus
    register_nav_menus([
        'dofs_topbar' => __('Dashboard Topbar Menu', 'dofs-theme'),
        'dofs_sidebar' => __('Dashboard Sidebar Menu', 'dofs-theme'),
        'dofs_user' => __('User Dropdown Menu', 'dofs-theme'),
        'dofs_services' => __('Services App Launcher', 'dofs-theme'),
    ]);
}
add_action('after_setup_theme', 'dofs_theme_setup');

/**
 * Enqueue theme assets
 */
function dofs_theme_assets() {
    // Don't load on admin pages
    if (is_admin()) {
        return;
    }

    // Main theme stylesheet
    wp_enqueue_style(
        'dofs-theme-style',
        DOFS_THEME_URI . '/assets/css/theme.css',
        [],
        DOFS_THEME_VERSION
    );

    // Theme JavaScript
    wp_enqueue_script(
        'dofs-theme-script',
        DOFS_THEME_URI . '/assets/js/theme.js',
        [],
        DOFS_THEME_VERSION,
        true
    );

    // Localize script with theme data
    wp_localize_script('dofs-theme-script', 'dofsTheme', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_rest'),
        'restUrl' => rest_url('sfs-hr/v1/dashboard/'),
        'user' => dofs_get_current_user_data(),
    ]);
}
add_action('wp_enqueue_scripts', 'dofs_theme_assets');

/**
 * Add editor styles for block editor compatibility
 */
function dofs_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'dofs_editor_styles');

/**
 * Get current user data for JavaScript
 */
function dofs_get_current_user_data(): array {
    if (!is_user_logged_in()) {
        return [];
    }

    $user = wp_get_current_user();
    return [
        'id' => $user->ID,
        'name' => $user->display_name,
        'email' => $user->user_email,
        'avatar' => get_avatar_url($user->ID, ['size' => 96]),
        'roles' => $user->roles,
    ];
}

/**
 * Register widget areas
 */
function dofs_theme_widgets_init() {
    register_sidebar([
        'name' => __('Dashboard Widgets', 'dofs-theme'),
        'id' => 'dashboard-widgets',
        'description' => __('Widgets displayed on the dashboard home page', 'dofs-theme'),
        'before_widget' => '<div id="%1$s" class="dashboard-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'dofs_theme_widgets_init');

/**
 * Get sidebar menu items (hierarchical)
 */
function dofs_get_sidebar_menu(): array {
    $locations = get_nav_menu_locations();

    if (!isset($locations['dofs_sidebar'])) {
        return dofs_get_default_sidebar_menu();
    }

    $menu = wp_get_nav_menu_object($locations['dofs_sidebar']);

    if (!$menu) {
        return dofs_get_default_sidebar_menu();
    }

    $items = wp_get_nav_menu_items($menu->term_id);

    if (!$items || empty($items)) {
        return dofs_get_default_sidebar_menu();
    }

    // Build hierarchical menu
    $sections = [];
    $children = [];

    foreach ($items as $item) {
        $menu_item = [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target ?: '_self',
            'icon' => dofs_get_menu_icon($item),
            'current' => $item->current || $item->current_item_ancestor,
        ];

        if ((int) $item->menu_item_parent === 0) {
            $sections[$item->ID] = [
                'section' => $item->title,
                'items' => [],
            ];
        } else {
            $children[$item->menu_item_parent][] = $menu_item;
        }
    }

    foreach ($children as $parent_id => $child_items) {
        if (isset($sections[$parent_id])) {
            $sections[$parent_id]['items'] = $child_items;
        }
    }

    $result = array_values(array_filter($sections, function($section) {
        return !empty($section['items']);
    }));

    return !empty($result) ? $result : dofs_get_default_sidebar_menu();
}

/**
 * Extract icon from menu item CSS classes
 */
function dofs_get_menu_icon($item): string {
    if (empty($item->classes)) {
        return 'document';
    }

    foreach ($item->classes as $class) {
        if (strpos($class, 'icon-') === 0) {
            return str_replace('icon-', '', $class);
        }
    }

    return 'document';
}

/**
 * Default sidebar menu - New unified structure
 */
function dofs_get_default_sidebar_menu(): array {
    return [
        [
            'section' => 'MAIN',
            'items' => [
                ['id' => 1, 'title' => __('Dashboard', 'dofs-theme'), 'url' => home_url('/'), 'icon' => 'home', 'current' => is_front_page()],
            ],
        ],
        [
            'section' => 'BUSINESS',
            'items' => [
                ['id' => 2, 'title' => __('CRM', 'dofs-theme'), 'url' => home_url('/crm/'), 'icon' => 'users'],
                ['id' => 3, 'title' => __('Sales & Orders', 'dofs-theme'), 'url' => home_url('/sales/'), 'icon' => 'chart'],
            ],
        ],
        [
            'section' => 'OPERATIONS',
            'items' => [
                ['id' => 4, 'title' => __('Measurements', 'dofs-theme'), 'url' => home_url('/measurements/'), 'icon' => 'ruler'],
                ['id' => 5, 'title' => __('Installation', 'dofs-theme'), 'url' => home_url('/installation/'), 'icon' => 'wrench'],
                ['id' => 6, 'title' => __('Design', 'dofs-theme'), 'url' => home_url('/design/'), 'icon' => 'pencil'],
                ['id' => 7, 'title' => __('Production', 'dofs-theme'), 'url' => home_url('/production/'), 'icon' => 'factory'],
                ['id' => 8, 'title' => __('Warehouse', 'dofs-theme'), 'url' => home_url('/warehouse/'), 'icon' => 'warehouse'],
                ['id' => 9, 'title' => __('Logistics', 'dofs-theme'), 'url' => home_url('/logistics/'), 'icon' => 'truck'],
            ],
        ],
        [
            'section' => 'MANAGEMENT',
            'items' => [
                ['id' => 10, 'title' => __('Projects', 'dofs-theme'), 'url' => home_url('/projects/'), 'icon' => 'grid'],
                ['id' => 11, 'title' => __('Maintenance', 'dofs-theme'), 'url' => home_url('/maintenance/'), 'icon' => 'tool'],
                ['id' => 12, 'title' => __('Reports & Analytics', 'dofs-theme'), 'url' => home_url('/reports/'), 'icon' => 'chart-bar'],
            ],
        ],
        [
            'section' => 'ADMINISTRATION',
            'items' => [
                ['id' => 13, 'title' => __('Administration', 'dofs-theme'), 'url' => home_url('/admin/'), 'icon' => 'settings'],
                ['id' => 14, 'title' => __('Human Resources', 'dofs-theme'), 'url' => home_url('/hr/'), 'icon' => 'user'],
            ],
        ],
        [
            'section' => 'SETTINGS',
            'items' => [
                ['id' => 15, 'title' => __('Settings', 'dofs-theme'), 'url' => home_url('/settings/'), 'icon' => 'cog'],
                ['id' => 16, 'title' => __('Help', 'dofs-theme'), 'url' => home_url('/help/'), 'icon' => 'help'],
            ],
        ],
    ];
}

/**
 * Get topbar menu items
 */
function dofs_get_topbar_menu(): array {
    $locations = get_nav_menu_locations();

    if (!isset($locations['dofs_topbar'])) {
        return [];
    }

    $menu = wp_get_nav_menu_object($locations['dofs_topbar']);

    if (!$menu) {
        return [];
    }

    $items = wp_get_nav_menu_items($menu->term_id);
    $menu_items = [];

    if ($items) {
        foreach ($items as $item) {
            $menu_items[] = [
                'id' => $item->ID,
                'title' => $item->title,
                'url' => $item->url,
                'target' => $item->target ?: '_self',
                'current' => $item->current,
            ];
        }
    }

    return $menu_items;
}

/**
 * Get services menu items for app launcher
 */
function dofs_get_services_menu(): array {
    $locations = get_nav_menu_locations();

    if (!isset($locations['dofs_services'])) {
        return dofs_get_default_services_menu();
    }

    $menu = wp_get_nav_menu_object($locations['dofs_services']);

    if (!$menu) {
        return dofs_get_default_services_menu();
    }

    $items = wp_get_nav_menu_items($menu->term_id);
    $services = [];

    if ($items) {
        foreach ($items as $item) {
            $services[] = [
                'id' => $item->ID,
                'title' => $item->title,
                'url' => $item->url,
                'icon' => dofs_get_menu_icon($item),
                'target' => '_blank',
                'image' => get_post_meta($item->ID, '_menu_item_image', true) ?: '',
            ];
        }
    }

    return !empty($services) ? $services : dofs_get_default_services_menu();
}

/**
 * Default services menu for app launcher
 */
function dofs_get_default_services_menu(): array {
    return [
        ['id' => 1, 'title' => __('Odoo ERP', 'dofs-theme'), 'url' => '#', 'icon' => 'external-link'],
        ['id' => 2, 'title' => __('Google Drive', 'dofs-theme'), 'url' => '#', 'icon' => 'external-link'],
        ['id' => 3, 'title' => __('Email', 'dofs-theme'), 'url' => '#', 'icon' => 'external-link'],
    ];
}

/**
 * Add body classes
 */
function dofs_body_classes($classes) {
    $classes[] = 'dofs-theme';

    if (is_user_logged_in()) {
        $classes[] = 'user-logged-in';
    }

    return $classes;
}
add_filter('body_class', 'dofs_body_classes');

/**
 * Get current section based on URL
 * Returns section slug if on a section page, false otherwise
 */
function dofs_get_current_section(): ?array {
    $sections = dofs_get_section_definitions();
    $current_url = trailingslashit($_SERVER['REQUEST_URI']);

    foreach ($sections as $slug => $section) {
        $section_path = '/' . $slug . '/';
        if (strpos($current_url, $section_path) === 0 || $current_url === $section_path) {
            return array_merge(['slug' => $slug], $section);
        }
    }

    return null;
}

/**
 * Get section definitions with their sub-navigation items
 * This can be filtered to add/modify sections
 */
function dofs_get_section_definitions(): array {
    $sections = [
        'crm' => [
            'title' => __('CRM', 'dofs-theme'),
            'icon' => 'users',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/crm/'), 'slug' => 'crm'],
                ['title' => __('All Customers', 'dofs-theme'), 'url' => home_url('/crm/customers/'), 'slug' => 'customers'],
                ['title' => __('New Customer', 'dofs-theme'), 'url' => home_url('/crm/new-customer/'), 'slug' => 'new-customer'],
                ['title' => __('New Entry', 'dofs-theme'), 'url' => home_url('/crm/new-entry/'), 'slug' => 'new-entry'],
                ['title' => __('New Invoice', 'dofs-theme'), 'url' => home_url('/crm/new-invoice/'), 'slug' => 'new-invoice'],
            ],
        ],
        'sales' => [
            'title' => __('Sales & Orders', 'dofs-theme'),
            'icon' => 'chart',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/sales/'), 'slug' => 'sales'],
                ['title' => __('All Orders', 'dofs-theme'), 'url' => home_url('/sales/orders/'), 'slug' => 'orders'],
                ['title' => __('Inbox', 'dofs-theme'), 'url' => home_url('/sales/inbox/'), 'slug' => 'inbox'],
                ['title' => __('My Customers', 'dofs-theme'), 'url' => home_url('/sales/my-customers/'), 'slug' => 'my-customers'],
                ['title' => __('Delivery Confirmation', 'dofs-theme'), 'url' => home_url('/sales/delivery-confirmation/'), 'slug' => 'delivery-confirmation'],
            ],
        ],
        'measurements' => [
            'title' => __('Measurements', 'dofs-theme'),
            'icon' => 'ruler',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/measurements/'), 'slug' => 'measurements'],
                ['title' => __('New Measurements', 'dofs-theme'), 'url' => home_url('/measurements/new/'), 'slug' => 'new'],
                ['title' => __('On Hold', 'dofs-theme'), 'url' => home_url('/measurements/on-hold/'), 'slug' => 'on-hold'],
                ['title' => __('Returned', 'dofs-theme'), 'url' => home_url('/measurements/returned/'), 'slug' => 'returned'],
            ],
        ],
        'installation' => [
            'title' => __('Installation', 'dofs-theme'),
            'icon' => 'wrench',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/installation/'), 'slug' => 'installation'],
                ['title' => __('New Jobs', 'dofs-theme'), 'url' => home_url('/installation/new/'), 'slug' => 'new'],
                ['title' => __('On Hold', 'dofs-theme'), 'url' => home_url('/installation/on-hold/'), 'slug' => 'on-hold'],
                ['title' => __('Loading', 'dofs-theme'), 'url' => home_url('/installation/loading/'), 'slug' => 'loading'],
                ['title' => __('Scheduling', 'dofs-theme'), 'url' => home_url('/installation/scheduling/'), 'slug' => 'scheduling'],
                ['title' => __('All Jobs', 'dofs-theme'), 'url' => home_url('/installation/all/'), 'slug' => 'all'],
            ],
        ],
        'design' => [
            'title' => __('Design', 'dofs-theme'),
            'icon' => 'pencil',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/design/'), 'slug' => 'design'],
                ['title' => __('New Drawing Job', 'dofs-theme'), 'url' => home_url('/design/new/'), 'slug' => 'new'],
                ['title' => __('Rejected Drawings', 'dofs-theme'), 'url' => home_url('/design/rejected/'), 'slug' => 'rejected'],
            ],
        ],
        'production' => [
            'title' => __('Production', 'dofs-theme'),
            'icon' => 'factory',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/production/'), 'slug' => 'production'],
                ['title' => __('Receiving', 'dofs-theme'), 'url' => home_url('/production/receiving/'), 'slug' => 'receiving'],
                ['title' => __('Preparing', 'dofs-theme'), 'url' => home_url('/production/preparing/'), 'slug' => 'preparing'],
                ['title' => __('Under Production', 'dofs-theme'), 'url' => home_url('/production/under-production/'), 'slug' => 'under-production'],
                ['title' => __('Entry Updating', 'dofs-theme'), 'url' => home_url('/production/entry-updating/'), 'slug' => 'entry-updating'],
                ['title' => __('Quality Rejection', 'dofs-theme'), 'url' => home_url('/production/quality-rejection/'), 'slug' => 'quality-rejection'],
                ['title' => __('CNC Operations', 'dofs-theme'), 'url' => home_url('/production/cnc/'), 'slug' => 'cnc'],
            ],
        ],
        'warehouse' => [
            'title' => __('Warehouse', 'dofs-theme'),
            'icon' => 'warehouse',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/warehouse/'), 'slug' => 'warehouse'],
                ['title' => __('Order Receiving', 'dofs-theme'), 'url' => home_url('/warehouse/order-receiving/'), 'slug' => 'order-receiving'],
                ['title' => __('Receiving', 'dofs-theme'), 'url' => home_url('/warehouse/receiving/'), 'slug' => 'receiving'],
                ['title' => __('Order Loading', 'dofs-theme'), 'url' => home_url('/warehouse/order-loading/'), 'slug' => 'order-loading'],
                ['title' => __('Quality Control', 'dofs-theme'), 'url' => home_url('/warehouse/quality-control/'), 'slug' => 'quality-control'],
            ],
        ],
        'logistics' => [
            'title' => __('Logistics', 'dofs-theme'),
            'icon' => 'truck',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/logistics/'), 'slug' => 'logistics'],
                ['title' => __('Goods Delivery', 'dofs-theme'), 'url' => home_url('/logistics/delivery/'), 'slug' => 'delivery'],
            ],
        ],
        'projects' => [
            'title' => __('Projects', 'dofs-theme'),
            'icon' => 'grid',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/projects/'), 'slug' => 'projects'],
                ['title' => __('All Projects', 'dofs-theme'), 'url' => home_url('/projects/all/'), 'slug' => 'all'],
                ['title' => __('New Project', 'dofs-theme'), 'url' => home_url('/projects/new/'), 'slug' => 'new'],
                ['title' => __('Project Drawings', 'dofs-theme'), 'url' => home_url('/projects/drawings/'), 'slug' => 'drawings'],
                ['title' => __('Manufacturing', 'dofs-theme'), 'url' => home_url('/projects/manufacturing/'), 'slug' => 'manufacturing'],
                ['title' => __('Delivery & Preparing', 'dofs-theme'), 'url' => home_url('/projects/delivery/'), 'slug' => 'delivery'],
            ],
        ],
        'maintenance' => [
            'title' => __('Maintenance', 'dofs-theme'),
            'icon' => 'tool',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/maintenance/'), 'slug' => 'maintenance'],
                ['title' => __('All Requests', 'dofs-theme'), 'url' => home_url('/maintenance/all/'), 'slug' => 'all'],
                ['title' => __('New Request', 'dofs-theme'), 'url' => home_url('/maintenance/new/'), 'slug' => 'new'],
                ['title' => __('Jobs', 'dofs-theme'), 'url' => home_url('/maintenance/jobs/'), 'slug' => 'jobs'],
            ],
        ],
        'reports' => [
            'title' => __('Reports & Analytics', 'dofs-theme'),
            'icon' => 'chart-bar',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/reports/'), 'slug' => 'reports'],
                ['title' => __('Sales Reports', 'dofs-theme'), 'url' => home_url('/reports/sales/'), 'slug' => 'sales'],
                ['title' => __('Order Reports', 'dofs-theme'), 'url' => home_url('/reports/orders/'), 'slug' => 'orders'],
                ['title' => __('Production Reports', 'dofs-theme'), 'url' => home_url('/reports/production/'), 'slug' => 'production'],
                ['title' => __('Custom Reports', 'dofs-theme'), 'url' => home_url('/reports/custom/'), 'slug' => 'custom'],
            ],
        ],
        'admin' => [
            'title' => __('Administration', 'dofs-theme'),
            'icon' => 'settings',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/admin/'), 'slug' => 'admin'],
                ['title' => __('Documents Library', 'dofs-theme'), 'url' => home_url('/admin/documents/'), 'slug' => 'documents'],
                ['title' => __('Delayed Entry', 'dofs-theme'), 'url' => home_url('/admin/delayed-entry/'), 'slug' => 'delayed-entry'],
                ['title' => __('DOFS Monitoring', 'dofs-theme'), 'url' => home_url('/admin/monitoring/'), 'slug' => 'monitoring'],
                ['title' => __('Installation Evaluation', 'dofs-theme'), 'url' => home_url('/admin/evaluation/'), 'slug' => 'evaluation'],
            ],
        ],
        'hr' => [
            'title' => __('Human Resources', 'dofs-theme'),
            'icon' => 'user',
            'items' => [
                ['title' => __('Overview', 'dofs-theme'), 'url' => home_url('/hr/'), 'slug' => 'hr'],
                ['title' => __('My HR', 'dofs-theme'), 'url' => home_url('/my-hr/'), 'slug' => 'my-hr'],
                ['title' => __('My Team', 'dofs-theme'), 'url' => home_url('/my-team/'), 'slug' => 'my-team'],
            ],
        ],
    ];

    return apply_filters('dofs_section_definitions', $sections);
}

/**
 * Render sub-navigation bar for current section
 */
function dofs_render_subnav(): void {
    $section = dofs_get_current_section();

    if (!$section || empty($section['items'])) {
        return;
    }

    $current_url = trailingslashit($_SERVER['REQUEST_URI']);
    ?>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 lg:px-6">
        <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide -mb-px">
            <?php foreach ($section['items'] as $item):
                $item_url = trailingslashit(wp_parse_url($item['url'], PHP_URL_PATH));
                $is_active = ($current_url === $item_url);
            ?>
            <a
                href="<?php echo esc_url($item['url']); ?>"
                class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition-colors <?php echo $is_active
                    ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'; ?>"
            >
                <?php echo esc_html($item['title']); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php
}

/**
 * Override WordPress admin bar margin on html element
 * WordPress adds margin-top: 32px to html which we need to remove
 */
function dofs_admin_bar_fix() {
    if (is_admin_bar_showing()) {
        ?>
        <style id="dofs-admin-bar-fix">
            html { margin-top: 0 !important; }
            * html body { margin-top: 0 !important; }
        </style>
        <?php
    }
}
add_action('wp_head', 'dofs_admin_bar_fix', 999);

/**
 * SVG Icon helper function
 */
function dofs_icon(string $name, string $class = 'w-5 h-5'): string {
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'grid' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
        'cart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        'cube' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
        'archive' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />',
        'team' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'trending' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
        'tasks' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'help' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />',
        'close' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />',
        'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />',
        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />',
        'sun' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />',
        'moon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />',
        // New icons for unified menu
        'ruler' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 2L2 6l14 14 4-4L6 2zm6.586 2.586l1.414 1.414M9.172 6l1.414 1.414M5.757 9.414l1.415 1.414" />',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />',
        'factory' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
        'warehouse' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
        'truck' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />',
        'tool' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />',
        'chart-bar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
        'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'apps' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
        'external-link' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
    ];

    $path = $icons[$name] ?? $icons['document'];

    return '<svg class="' . esc_attr($class) . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' . $path . '</svg>';
}

/**
 * Load Dashboard REST API Module
 */
function dofs_load_dashboard_module() {
    $dashboard_module = DOFS_THEME_DIR . '/includes/Dashboard/DashboardModule.php';

    if (file_exists($dashboard_module)) {
        require_once $dashboard_module;

        $dashboard = new \SFS_HR\Dashboard\DashboardModule();
        $dashboard->init();
    }
}
add_action('after_setup_theme', 'dofs_load_dashboard_module');

/**
 * Register dashboard capabilities and create required pages on theme activation
 */
function dofs_theme_activation() {
    $admin = get_role('administrator');
    if ($admin) {
        // Dashboard capabilities
        $admin->add_cap('dofs.view_dashboard');
        $admin->add_cap('dofs.view_sales');
        $admin->add_cap('dofs.view_orders');
        $admin->add_cap('dofs.view_reports');
        $admin->add_cap('dofs.view_inventory');

        // HR capabilities
        $admin->add_cap('sfs_hr.view_self');
        $admin->add_cap('sfs_hr.view_team');
        $admin->add_cap('sfs_hr.approve_leave');
        $admin->add_cap('sfs_hr.approve_loan');
        $admin->add_cap('sfs_hr.view_dashboard_manager');
    }

    // Create required pages
    dofs_create_required_pages();

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'dofs_theme_activation');

/**
 * Create required pages (Settings, Help) if they don't exist
 */
function dofs_create_required_pages() {
    $pages = [
        [
            'slug' => 'settings',
            'title' => __('Settings', 'dofs-theme'),
            'template' => 'page-settings.php',
        ],
        [
            'slug' => 'help',
            'title' => __('Help', 'dofs-theme'),
            'template' => 'page-help.php',
        ],
    ];

    foreach ($pages as $page_data) {
        // Check if page exists
        $existing = get_page_by_path($page_data['slug']);

        if (!$existing) {
            // Create the page
            $page_id = wp_insert_post([
                'post_title' => $page_data['title'],
                'post_name' => $page_data['slug'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
            ]);

            // Set page template if specified
            if ($page_id && !is_wp_error($page_id) && !empty($page_data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
}

/**
 * Handle AJAX settings save
 */
function dofs_save_settings() {
    // Verify nonce
    if (!isset($_POST['dofs_settings_nonce']) || !wp_verify_nonce($_POST['dofs_settings_nonce'], 'dofs_save_settings')) {
        wp_send_json_error(__('Security check failed', 'dofs-theme'));
    }

    // Check user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(__('You must be logged in', 'dofs-theme'));
    }

    $user_id = get_current_user_id();

    // Sanitize and save settings
    $settings_to_save = [
        'dofs_theme_preference' => sanitize_text_field($_POST['theme'] ?? 'system'),
        'dofs_language' => sanitize_text_field($_POST['language'] ?? 'en_US'),
        'dofs_date_format' => sanitize_text_field($_POST['date_format'] ?? 'd/m/Y'),
        'dofs_time_format' => sanitize_text_field($_POST['time_format'] ?? 'g:i a'),
        'dofs_timezone' => sanitize_text_field($_POST['timezone'] ?? 'UTC'),
        'dofs_currency' => sanitize_text_field($_POST['currency'] ?? 'SAR'),
        'dofs_number_format' => sanitize_text_field($_POST['number_format'] ?? 'en'),
        'dofs_notifications_email' => isset($_POST['notifications_email']) ? '1' : '0',
        'dofs_notifications_push' => isset($_POST['notifications_push']) ? '1' : '0',
        'dofs_notifications_frequency' => sanitize_text_field($_POST['notifications_frequency'] ?? 'instant'),
    ];

    foreach ($settings_to_save as $meta_key => $meta_value) {
        update_user_meta($user_id, $meta_key, $meta_value);
    }

    // Handle Quick Access visibility (save hidden items)
    $all_quick_access = ['sales', 'orders', 'hr', 'reports', 'inventory', 'customers'];
    $visible_items = isset($_POST['quick_access_visible']) && is_array($_POST['quick_access_visible'])
        ? array_map('sanitize_text_field', $_POST['quick_access_visible'])
        : [];
    $hidden_items = array_diff($all_quick_access, $visible_items);
    update_user_meta($user_id, 'dofs_quick_access_hidden', array_values($hidden_items));

    wp_send_json_success(__('Settings saved successfully', 'dofs-theme'));
}
add_action('wp_ajax_dofs_save_settings', 'dofs_save_settings');

/**
 * Get user setting value with default fallback
 */
function dofs_get_user_setting(string $key, $default = '') {
    if (!is_user_logged_in()) {
        return $default;
    }

    $value = get_user_meta(get_current_user_id(), 'dofs_' . $key, true);
    return $value !== '' ? $value : $default;
}

/**
 * =============================================================================
 * NOTIFICATION INTEGRATION HOOKS
 *
 * These filters allow notification plugins (Simple HR Suite, Simple Notification,
 * Simple Flow Report) to check user preferences before sending notifications.
 * =============================================================================
 */

/**
 * Check if user should receive email notifications
 *
 * Usage in plugins:
 * if (apply_filters('dofs_user_wants_email_notification', true, $user_id, $notification_type)) {
 *     // Send email
 * }
 *
 * @param bool   $should_send     Default true
 * @param int    $user_id         User ID to check
 * @param string $notification_type Type of notification (e.g., 'hr_request', 'flow_task', 'report')
 * @return bool
 */
function dofs_filter_email_notification($should_send, $user_id, $notification_type = '') {
    $enabled = get_user_meta($user_id, 'dofs_notifications_email', true);

    // Default to enabled if not set
    if ($enabled === '') {
        return $should_send;
    }

    return $enabled === '1';
}
add_filter('dofs_user_wants_email_notification', 'dofs_filter_email_notification', 10, 3);

/**
 * Check if user should receive push notifications
 *
 * Usage in plugins:
 * if (apply_filters('dofs_user_wants_push_notification', true, $user_id, $notification_type)) {
 *     // Send push notification
 * }
 *
 * @param bool   $should_send     Default true
 * @param int    $user_id         User ID to check
 * @param string $notification_type Type of notification
 * @return bool
 */
function dofs_filter_push_notification($should_send, $user_id, $notification_type = '') {
    $enabled = get_user_meta($user_id, 'dofs_notifications_push', true);

    // Default to enabled if not set
    if ($enabled === '') {
        return $should_send;
    }

    return $enabled === '1';
}
add_filter('dofs_user_wants_push_notification', 'dofs_filter_push_notification', 10, 3);

/**
 * Get user's notification frequency preference
 *
 * Usage in plugins:
 * $frequency = apply_filters('dofs_user_notification_frequency', 'instant', $user_id);
 * // Returns: 'instant', 'daily', or 'weekly'
 *
 * @param string $default  Default frequency
 * @param int    $user_id  User ID to check
 * @return string 'instant', 'daily', or 'weekly'
 */
function dofs_filter_notification_frequency($default, $user_id) {
    $frequency = get_user_meta($user_id, 'dofs_notifications_frequency', true);

    if (empty($frequency)) {
        return $default;
    }

    return $frequency;
}
add_filter('dofs_user_notification_frequency', 'dofs_filter_notification_frequency', 10, 2);

/**
 * Check if notification should be sent now based on frequency
 *
 * Usage in plugins:
 * if (apply_filters('dofs_should_send_notification_now', true, $user_id, $notification_type)) {
 *     // Send immediately
 * } else {
 *     // Queue for digest
 * }
 *
 * @param bool   $should_send_now Default true
 * @param int    $user_id         User ID
 * @param string $notification_type Type of notification
 * @return bool
 */
function dofs_filter_should_send_now($should_send_now, $user_id, $notification_type = '') {
    $frequency = get_user_meta($user_id, 'dofs_notifications_frequency', true);

    // If instant or not set, send immediately
    if (empty($frequency) || $frequency === 'instant') {
        return true;
    }

    // For daily/weekly, don't send immediately (queue for digest)
    return false;
}
add_filter('dofs_should_send_notification_now', 'dofs_filter_should_send_now', 10, 3);

/**
 * Get all notification preferences for a user
 *
 * Usage in plugins:
 * $prefs = apply_filters('dofs_get_user_notification_preferences', [], $user_id);
 *
 * @param array $preferences Default empty array
 * @param int   $user_id     User ID
 * @return array
 */
function dofs_get_notification_preferences($preferences, $user_id) {
    return [
        'email_enabled' => get_user_meta($user_id, 'dofs_notifications_email', true) !== '0',
        'push_enabled' => get_user_meta($user_id, 'dofs_notifications_push', true) !== '0',
        'frequency' => get_user_meta($user_id, 'dofs_notifications_frequency', true) ?: 'instant',
    ];
}
add_filter('dofs_get_user_notification_preferences', 'dofs_get_notification_preferences', 10, 2);

/**
 * =============================================================================
 * ADMIN SETTINGS
 * =============================================================================
 */

/**
 * Load Admin Settings Module
 */
require_once DOFS_THEME_DIR . '/inc/admin-settings.php';
