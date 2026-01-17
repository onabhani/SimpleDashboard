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
    ]);
}
add_action('after_setup_theme', 'dofs_theme_setup');

/**
 * Enqueue theme assets
 */
function dofs_theme_assets() {
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
    wp_localize_script('dofs-theme-script', 'DOFS_THEME', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dofs_theme_nonce'),
        'rest_url' => rest_url('dofs/v1/'),
        'user' => dofs_get_current_user_data(),
    ]);
}
add_action('wp_enqueue_scripts', 'dofs_theme_assets');

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
 * Default sidebar menu
 */
function dofs_get_default_sidebar_menu(): array {
    return [
        [
            'section' => 'MAIN',
            'items' => [
                ['id' => 1, 'title' => 'Dashboard', 'url' => home_url('/'), 'icon' => 'home', 'current' => is_front_page()],
                ['id' => 2, 'title' => 'Quick Access', 'url' => '#quick-access', 'icon' => 'grid'],
            ],
        ],
        [
            'section' => 'SALES & ORDERS',
            'items' => [
                ['id' => 3, 'title' => 'Sales Overview', 'url' => home_url('/sales/'), 'icon' => 'chart'],
                ['id' => 4, 'title' => 'Orders', 'url' => home_url('/orders/'), 'icon' => 'cart'],
                ['id' => 5, 'title' => 'Customers', 'url' => home_url('/customers/'), 'icon' => 'users'],
            ],
        ],
        [
            'section' => 'INVENTORY',
            'items' => [
                ['id' => 6, 'title' => 'Products', 'url' => home_url('/products/'), 'icon' => 'cube'],
                ['id' => 7, 'title' => 'Stock Levels', 'url' => home_url('/stock/'), 'icon' => 'archive'],
            ],
        ],
        [
            'section' => 'HR',
            'items' => [
                ['id' => 8, 'title' => 'HR Overview', 'url' => home_url('/hr/'), 'icon' => 'team'],
                ['id' => 9, 'title' => 'My HR', 'url' => home_url('/my-hr/'), 'icon' => 'user'],
                ['id' => 10, 'title' => 'My Team', 'url' => home_url('/my-team/'), 'icon' => 'users'],
                ['id' => 11, 'title' => 'HR Actions', 'url' => home_url('/hr-actions/'), 'icon' => 'clipboard'],
            ],
        ],
        [
            'section' => 'REPORTS & DATA',
            'items' => [
                ['id' => 12, 'title' => 'Reports', 'url' => home_url('/reports/'), 'icon' => 'document'],
                ['id' => 13, 'title' => 'Analytics', 'url' => home_url('/analytics/'), 'icon' => 'trending'],
            ],
        ],
        [
            'section' => 'WORKFLOWS',
            'items' => [
                ['id' => 14, 'title' => 'My Tasks', 'url' => home_url('/tasks/'), 'icon' => 'tasks'],
            ],
        ],
        [
            'section' => 'SETTINGS',
            'items' => [
                ['id' => 15, 'title' => 'Settings', 'url' => home_url('/settings/'), 'icon' => 'settings'],
                ['id' => 16, 'title' => 'Help', 'url' => home_url('/help/'), 'icon' => 'help'],
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
    ];

    $path = $icons[$name] ?? $icons['document'];

    return '<svg class="' . esc_attr($class) . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' . $path . '</svg>';
}
