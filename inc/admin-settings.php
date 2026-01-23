<?php
/**
 * DOFS Dashboard Admin Settings
 *
 * Provides an admin interface to configure dashboard items
 *
 * @package DOFS_Theme
 */

defined('ABSPATH') || exit;

/**
 * Register admin menu
 */
function dofs_register_admin_menu(): void {
    add_menu_page(
        __('Dashboard Settings', 'dofs-theme'),
        __('DOFS Dashboard', 'dofs-theme'),
        'manage_options',
        'dofs-dashboard-settings',
        'dofs_render_admin_settings_page',
        'dashicons-layout',
        59
    );

    add_submenu_page(
        'dofs-dashboard-settings',
        __('Quick Access', 'dofs-theme'),
        __('Quick Access', 'dofs-theme'),
        'manage_options',
        'dofs-quick-access',
        'dofs_render_quick_access_page'
    );

    add_submenu_page(
        'dofs-dashboard-settings',
        __('Quick Actions', 'dofs-theme'),
        __('Quick Actions', 'dofs-theme'),
        'manage_options',
        'dofs-quick-actions',
        'dofs_render_quick_actions_page'
    );

    add_submenu_page(
        'dofs-dashboard-settings',
        __('Services', 'dofs-theme'),
        __('Services', 'dofs-theme'),
        'manage_options',
        'dofs-services',
        'dofs_render_services_page'
    );
}
add_action('admin_menu', 'dofs_register_admin_menu');

/**
 * Register settings
 */
function dofs_register_settings(): void {
    register_setting('dofs_dashboard_settings', 'dofs_quick_access_items', [
        'type' => 'array',
        'sanitize_callback' => 'dofs_sanitize_items_array',
        'default' => [],
    ]);

    register_setting('dofs_dashboard_settings', 'dofs_quick_actions_items', [
        'type' => 'array',
        'sanitize_callback' => 'dofs_sanitize_items_array',
        'default' => [],
    ]);

    register_setting('dofs_dashboard_settings', 'dofs_services_items', [
        'type' => 'array',
        'sanitize_callback' => 'dofs_sanitize_items_array',
        'default' => [],
    ]);
}
add_action('admin_init', 'dofs_register_settings');

/**
 * Sanitize items array
 */
function dofs_sanitize_items_array($items): array {
    if (!is_array($items)) {
        return [];
    }

    $sanitized = [];
    foreach ($items as $item) {
        if (empty($item['title'])) {
            continue;
        }

        $sanitized[] = [
            'id' => sanitize_key($item['id'] ?? sanitize_title($item['title'])),
            'title' => sanitize_text_field($item['title']),
            'url' => esc_url_raw($item['url'] ?? ''),
            'icon' => sanitize_key($item['icon'] ?? 'grid'),
            'gradient' => sanitize_text_field($item['gradient'] ?? ''),
            'color' => sanitize_hex_color($item['color'] ?? ''),
            'enabled' => !empty($item['enabled']),
        ];
    }

    return $sanitized;
}

/**
 * Enqueue admin scripts and styles
 */
function dofs_admin_enqueue_scripts($hook): void {
    if (strpos($hook, 'dofs-') === false) {
        return;
    }

    wp_enqueue_style('dofs-admin', get_template_directory_uri() . '/assets/css/admin.css', [], '1.0.0');
    wp_enqueue_script('dofs-admin', get_template_directory_uri() . '/assets/js/admin.js', ['jquery', 'jquery-ui-sortable'], '1.0.0', true);

    wp_localize_script('dofs-admin', 'dofsAdmin', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dofs_admin_nonce'),
        'strings' => [
            'confirmDelete' => __('Are you sure you want to delete this item?', 'dofs-theme'),
            'saved' => __('Settings saved successfully!', 'dofs-theme'),
            'error' => __('An error occurred. Please try again.', 'dofs-theme'),
        ],
    ]);
}
add_action('admin_enqueue_scripts', 'dofs_admin_enqueue_scripts');

/**
 * Get available icons
 */
function dofs_get_available_icons(): array {
    return [
        'home' => __('Home', 'dofs-theme'),
        'users' => __('Users', 'dofs-theme'),
        'chart' => __('Chart', 'dofs-theme'),
        'chart-bar' => __('Chart Bar', 'dofs-theme'),
        'cart' => __('Cart', 'dofs-theme'),
        'document' => __('Document', 'dofs-theme'),
        'grid' => __('Grid', 'dofs-theme'),
        'settings' => __('Settings', 'dofs-theme'),
        'bell' => __('Bell', 'dofs-theme'),
        'calendar' => __('Calendar', 'dofs-theme'),
        'mail' => __('Mail', 'dofs-theme'),
        'search' => __('Search', 'dofs-theme'),
        'user' => __('User', 'dofs-theme'),
        'team' => __('Team', 'dofs-theme'),
        'factory' => __('Factory', 'dofs-theme'),
        'warehouse' => __('Warehouse', 'dofs-theme'),
        'truck' => __('Truck', 'dofs-theme'),
        'tool' => __('Tool', 'dofs-theme'),
        'wrench' => __('Wrench', 'dofs-theme'),
        'ruler' => __('Ruler', 'dofs-theme'),
        'pencil' => __('Pencil', 'dofs-theme'),
        'external-link' => __('External Link', 'dofs-theme'),
        'tasks' => __('Tasks', 'dofs-theme'),
        'trending' => __('Trending', 'dofs-theme'),
    ];
}

/**
 * Get gradient options
 */
function dofs_get_gradient_options(): array {
    return [
        'from-blue-500 to-blue-600' => __('Blue', 'dofs-theme'),
        'from-purple-500 to-purple-600' => __('Purple', 'dofs-theme'),
        'from-green-500 to-green-600' => __('Green', 'dofs-theme'),
        'from-orange-500 to-orange-600' => __('Orange', 'dofs-theme'),
        'from-pink-500 to-pink-600' => __('Pink', 'dofs-theme'),
        'from-cyan-500 to-cyan-600' => __('Cyan', 'dofs-theme'),
        'from-red-500 to-red-600' => __('Red', 'dofs-theme'),
        'from-yellow-500 to-yellow-600' => __('Yellow', 'dofs-theme'),
        'from-indigo-500 to-indigo-600' => __('Indigo', 'dofs-theme'),
        'from-teal-500 to-teal-600' => __('Teal', 'dofs-theme'),
    ];
}

/**
 * Render main admin settings page
 */
function dofs_render_admin_settings_page(): void {
    ?>
    <div class="wrap dofs-admin-wrap">
        <h1><?php esc_html_e('DOFS Dashboard Settings', 'dofs-theme'); ?></h1>

        <div class="dofs-admin-cards">
            <div class="dofs-admin-card">
                <div class="dofs-admin-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <span class="dashicons dashicons-screenoptions"></span>
                </div>
                <h2><?php esc_html_e('Quick Access', 'dofs-theme'); ?></h2>
                <p><?php esc_html_e('Configure the Quick Access cards shown on the dashboard homepage.', 'dofs-theme'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-quick-access')); ?>" class="button button-primary">
                    <?php esc_html_e('Configure', 'dofs-theme'); ?>
                </a>
            </div>

            <div class="dofs-admin-card">
                <div class="dofs-admin-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <span class="dashicons dashicons-editor-ul"></span>
                </div>
                <h2><?php esc_html_e('Quick Actions', 'dofs-theme'); ?></h2>
                <p><?php esc_html_e('Configure the Quick Action buttons shown below the Quick Access cards.', 'dofs-theme'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-quick-actions')); ?>" class="button button-primary">
                    <?php esc_html_e('Configure', 'dofs-theme'); ?>
                </a>
            </div>

            <div class="dofs-admin-card">
                <div class="dofs-admin-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <span class="dashicons dashicons-networking"></span>
                </div>
                <h2><?php esc_html_e('Services', 'dofs-theme'); ?></h2>
                <p><?php esc_html_e('Configure the external services shown in the App Launcher dropdown.', 'dofs-theme'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-services')); ?>" class="button button-primary">
                    <?php esc_html_e('Configure', 'dofs-theme'); ?>
                </a>
            </div>

            <div class="dofs-admin-card">
                <div class="dofs-admin-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <span class="dashicons dashicons-admin-appearance"></span>
                </div>
                <h2><?php esc_html_e('Menus', 'dofs-theme'); ?></h2>
                <p><?php esc_html_e('Configure sidebar navigation and menu items using WordPress Menus.', 'dofs-theme'); ?></p>
                <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button button-primary">
                    <?php esc_html_e('Configure', 'dofs-theme'); ?>
                </a>
            </div>
        </div>

        <div class="dofs-admin-info">
            <h3><?php esc_html_e('Theme Information', 'dofs-theme'); ?></h3>
            <table class="widefat">
                <tr>
                    <th><?php esc_html_e('Theme Name', 'dofs-theme'); ?></th>
                    <td>DOFS Dashboard Theme</td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Version', 'dofs-theme'); ?></th>
                    <td>1.0.0</td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Menu Locations', 'dofs-theme'); ?></th>
                    <td>
                        <code>dofs_sidebar</code> - <?php esc_html_e('Sidebar Navigation', 'dofs-theme'); ?><br>
                        <code>dofs_services</code> - <?php esc_html_e('Services (App Launcher)', 'dofs-theme'); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Render Quick Access settings page
 */
function dofs_render_quick_access_page(): void {
    $items = get_option('dofs_quick_access_items', []);

    // Default items if empty
    if (empty($items)) {
        $items = [
            ['id' => 'crm', 'title' => 'CRM', 'url' => home_url('/crm/'), 'icon' => 'users', 'gradient' => 'from-blue-500 to-blue-600', 'enabled' => true],
            ['id' => 'sales', 'title' => 'Sales & Orders', 'url' => home_url('/sales/'), 'icon' => 'chart', 'gradient' => 'from-purple-500 to-purple-600', 'enabled' => true],
            ['id' => 'production', 'title' => 'Production', 'url' => home_url('/production/'), 'icon' => 'factory', 'gradient' => 'from-orange-500 to-orange-600', 'enabled' => true],
            ['id' => 'warehouse', 'title' => 'Warehouse', 'url' => home_url('/warehouse/'), 'icon' => 'warehouse', 'gradient' => 'from-green-500 to-green-600', 'enabled' => true],
            ['id' => 'projects', 'title' => 'Projects', 'url' => home_url('/projects/'), 'icon' => 'grid', 'gradient' => 'from-pink-500 to-pink-600', 'enabled' => true],
            ['id' => 'reports', 'title' => 'Reports', 'url' => home_url('/reports/'), 'icon' => 'chart-bar', 'gradient' => 'from-cyan-500 to-cyan-600', 'enabled' => true],
        ];
    }

    $icons = dofs_get_available_icons();
    $gradients = dofs_get_gradient_options();

    if (isset($_POST['dofs_save_quick_access']) && wp_verify_nonce($_POST['_wpnonce'], 'dofs_quick_access_nonce')) {
        $new_items = [];
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (empty($item['title'])) continue;
                $new_items[] = [
                    'id' => sanitize_key($item['id'] ?? sanitize_title($item['title'])),
                    'title' => sanitize_text_field($item['title']),
                    'url' => esc_url_raw($item['url']),
                    'icon' => sanitize_key($item['icon']),
                    'gradient' => sanitize_text_field($item['gradient']),
                    'enabled' => !empty($item['enabled']),
                ];
            }
        }
        update_option('dofs_quick_access_items', $new_items);
        $items = $new_items;
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'dofs-theme') . '</p></div>';
    }
    ?>
    <div class="wrap dofs-admin-wrap">
        <h1>
            <?php esc_html_e('Quick Access Configuration', 'dofs-theme'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-dashboard-settings')); ?>" class="page-title-action"><?php esc_html_e('Back to Dashboard', 'dofs-theme'); ?></a>
        </h1>
        <p class="description"><?php esc_html_e('Configure the Quick Access cards displayed on the dashboard. Drag to reorder.', 'dofs-theme'); ?></p>

        <form method="post" id="dofs-quick-access-form">
            <?php wp_nonce_field('dofs_quick_access_nonce'); ?>

            <div class="dofs-items-list" id="quick-access-items">
                <?php foreach ($items as $index => $item): ?>
                <div class="dofs-item-row" data-index="<?php echo esc_attr($index); ?>">
                    <div class="dofs-item-handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                    <div class="dofs-item-fields">
                        <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo esc_attr($item['id'] ?? ''); ?>">

                        <div class="dofs-field">
                            <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                            <input type="text" name="items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" required>
                        </div>

                        <div class="dofs-field">
                            <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                            <input type="url" name="items[<?php echo $index; ?>][url]" value="<?php echo esc_url($item['url']); ?>" placeholder="https://">
                        </div>

                        <div class="dofs-field dofs-field-small">
                            <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                            <select name="items[<?php echo $index; ?>][icon]">
                                <?php foreach ($icons as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($item['icon'] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="dofs-field dofs-field-small">
                            <label><?php esc_html_e('Color', 'dofs-theme'); ?></label>
                            <select name="items[<?php echo $index; ?>][gradient]">
                                <?php foreach ($gradients as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($item['gradient'] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="dofs-field dofs-field-checkbox">
                            <label>
                                <input type="checkbox" name="items[<?php echo $index; ?>][enabled]" value="1" <?php checked($item['enabled'] ?? true); ?>>
                                <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dofs-item-actions">
                        <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="dofs-add-item-wrap">
                <button type="button" class="button" id="add-quick-access-item">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e('Add Item', 'dofs-theme'); ?>
                </button>
            </div>

            <p class="submit">
                <input type="submit" name="dofs_save_quick_access" class="button button-primary button-large" value="<?php esc_attr_e('Save Changes', 'dofs-theme'); ?>">
            </p>
        </form>
    </div>

    <script type="text/html" id="tmpl-quick-access-item">
        <div class="dofs-item-row" data-index="{{data.index}}">
            <div class="dofs-item-handle">
                <span class="dashicons dashicons-menu"></span>
            </div>
            <div class="dofs-item-fields">
                <input type="hidden" name="items[{{data.index}}][id]" value="">

                <div class="dofs-field">
                    <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                    <input type="text" name="items[{{data.index}}][title]" value="" required>
                </div>

                <div class="dofs-field">
                    <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                    <input type="url" name="items[{{data.index}}][url]" value="" placeholder="https://">
                </div>

                <div class="dofs-field dofs-field-small">
                    <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                    <select name="items[{{data.index}}][icon]">
                        <?php foreach ($icons as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="dofs-field dofs-field-small">
                    <label><?php esc_html_e('Color', 'dofs-theme'); ?></label>
                    <select name="items[{{data.index}}][gradient]">
                        <?php foreach ($gradients as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="dofs-field dofs-field-checkbox">
                    <label>
                        <input type="checkbox" name="items[{{data.index}}][enabled]" value="1" checked>
                        <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                    </label>
                </div>
            </div>
            <div class="dofs-item-actions">
                <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
    </script>
    <?php
}

/**
 * Render Quick Actions settings page
 */
function dofs_render_quick_actions_page(): void {
    $items = get_option('dofs_quick_actions_items', []);

    // Default items if empty
    if (empty($items)) {
        $items = [
            ['id' => 'all-orders', 'title' => 'All Orders', 'url' => home_url('/sales/orders/'), 'icon' => 'chart', 'enabled' => true],
            ['id' => 'new-customer', 'title' => 'New Customer', 'url' => home_url('/crm/new-customer/'), 'icon' => 'users', 'enabled' => true],
            ['id' => 'new-entry', 'title' => 'New Entry', 'url' => home_url('/crm/new-entry/'), 'icon' => 'document', 'enabled' => true],
            ['id' => 'new-invoice', 'title' => 'New Invoice', 'url' => home_url('/crm/new-invoice/'), 'icon' => 'cart', 'enabled' => true],
            ['id' => 'new-project', 'title' => 'New Project', 'url' => home_url('/projects/new/'), 'icon' => 'grid', 'enabled' => true],
            ['id' => 'new-maintenance', 'title' => 'New Maintenance', 'url' => home_url('/maintenance/new/'), 'icon' => 'tool', 'enabled' => true],
            ['id' => 'view-reports', 'title' => 'View Reports', 'url' => home_url('/reports/'), 'icon' => 'chart-bar', 'enabled' => true],
        ];
    }

    $icons = dofs_get_available_icons();

    if (isset($_POST['dofs_save_quick_actions']) && wp_verify_nonce($_POST['_wpnonce'], 'dofs_quick_actions_nonce')) {
        $new_items = [];
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (empty($item['title'])) continue;
                $new_items[] = [
                    'id' => sanitize_key($item['id'] ?? sanitize_title($item['title'])),
                    'title' => sanitize_text_field($item['title']),
                    'url' => esc_url_raw($item['url']),
                    'icon' => sanitize_key($item['icon']),
                    'enabled' => !empty($item['enabled']),
                ];
            }
        }
        update_option('dofs_quick_actions_items', $new_items);
        $items = $new_items;
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'dofs-theme') . '</p></div>';
    }
    ?>
    <div class="wrap dofs-admin-wrap">
        <h1>
            <?php esc_html_e('Quick Actions Configuration', 'dofs-theme'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-dashboard-settings')); ?>" class="page-title-action"><?php esc_html_e('Back to Dashboard', 'dofs-theme'); ?></a>
        </h1>
        <p class="description"><?php esc_html_e('Configure the Quick Action buttons displayed below the Quick Access cards. Drag to reorder.', 'dofs-theme'); ?></p>

        <form method="post" id="dofs-quick-actions-form">
            <?php wp_nonce_field('dofs_quick_actions_nonce'); ?>

            <div class="dofs-items-list" id="quick-actions-items">
                <?php foreach ($items as $index => $item): ?>
                <div class="dofs-item-row" data-index="<?php echo esc_attr($index); ?>">
                    <div class="dofs-item-handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                    <div class="dofs-item-fields">
                        <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo esc_attr($item['id'] ?? ''); ?>">

                        <div class="dofs-field">
                            <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                            <input type="text" name="items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" required>
                        </div>

                        <div class="dofs-field">
                            <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                            <input type="url" name="items[<?php echo $index; ?>][url]" value="<?php echo esc_url($item['url']); ?>" placeholder="https://">
                        </div>

                        <div class="dofs-field dofs-field-small">
                            <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                            <select name="items[<?php echo $index; ?>][icon]">
                                <?php foreach ($icons as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($item['icon'] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="dofs-field dofs-field-checkbox">
                            <label>
                                <input type="checkbox" name="items[<?php echo $index; ?>][enabled]" value="1" <?php checked($item['enabled'] ?? true); ?>>
                                <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dofs-item-actions">
                        <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="dofs-add-item-wrap">
                <button type="button" class="button" id="add-quick-actions-item">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e('Add Item', 'dofs-theme'); ?>
                </button>
            </div>

            <p class="submit">
                <input type="submit" name="dofs_save_quick_actions" class="button button-primary button-large" value="<?php esc_attr_e('Save Changes', 'dofs-theme'); ?>">
            </p>
        </form>
    </div>

    <script type="text/html" id="tmpl-quick-actions-item">
        <div class="dofs-item-row" data-index="{{data.index}}">
            <div class="dofs-item-handle">
                <span class="dashicons dashicons-menu"></span>
            </div>
            <div class="dofs-item-fields">
                <input type="hidden" name="items[{{data.index}}][id]" value="">

                <div class="dofs-field">
                    <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                    <input type="text" name="items[{{data.index}}][title]" value="" required>
                </div>

                <div class="dofs-field">
                    <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                    <input type="url" name="items[{{data.index}}][url]" value="" placeholder="https://">
                </div>

                <div class="dofs-field dofs-field-small">
                    <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                    <select name="items[{{data.index}}][icon]">
                        <?php foreach ($icons as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="dofs-field dofs-field-checkbox">
                    <label>
                        <input type="checkbox" name="items[{{data.index}}][enabled]" value="1" checked>
                        <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                    </label>
                </div>
            </div>
            <div class="dofs-item-actions">
                <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
    </script>
    <?php
}

/**
 * Render Services settings page
 */
function dofs_render_services_page(): void {
    $items = get_option('dofs_services_items', []);

    // Default items if empty
    if (empty($items)) {
        $items = [
            ['id' => 'google', 'title' => 'Google', 'url' => 'https://google.com', 'icon' => 'search', 'enabled' => true],
            ['id' => 'gmail', 'title' => 'Gmail', 'url' => 'https://mail.google.com', 'icon' => 'mail', 'enabled' => true],
            ['id' => 'calendar', 'title' => 'Calendar', 'url' => 'https://calendar.google.com', 'icon' => 'calendar', 'enabled' => true],
        ];
    }

    $icons = dofs_get_available_icons();

    if (isset($_POST['dofs_save_services']) && wp_verify_nonce($_POST['_wpnonce'], 'dofs_services_nonce')) {
        $new_items = [];
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (empty($item['title'])) continue;
                $new_items[] = [
                    'id' => sanitize_key($item['id'] ?? sanitize_title($item['title'])),
                    'title' => sanitize_text_field($item['title']),
                    'url' => esc_url_raw($item['url']),
                    'icon' => sanitize_key($item['icon']),
                    'enabled' => !empty($item['enabled']),
                ];
            }
        }
        update_option('dofs_services_items', $new_items);
        $items = $new_items;
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'dofs-theme') . '</p></div>';
    }
    ?>
    <div class="wrap dofs-admin-wrap">
        <h1>
            <?php esc_html_e('Services Configuration', 'dofs-theme'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dofs-dashboard-settings')); ?>" class="page-title-action"><?php esc_html_e('Back to Dashboard', 'dofs-theme'); ?></a>
        </h1>
        <p class="description"><?php esc_html_e('Configure external services shown in the App Launcher dropdown (grid icon in header). Drag to reorder.', 'dofs-theme'); ?></p>

        <form method="post" id="dofs-services-form">
            <?php wp_nonce_field('dofs_services_nonce'); ?>

            <div class="dofs-items-list" id="services-items">
                <?php foreach ($items as $index => $item): ?>
                <div class="dofs-item-row" data-index="<?php echo esc_attr($index); ?>">
                    <div class="dofs-item-handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                    <div class="dofs-item-fields">
                        <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo esc_attr($item['id'] ?? ''); ?>">

                        <div class="dofs-field">
                            <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                            <input type="text" name="items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" required>
                        </div>

                        <div class="dofs-field">
                            <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                            <input type="url" name="items[<?php echo $index; ?>][url]" value="<?php echo esc_url($item['url']); ?>" placeholder="https://" required>
                        </div>

                        <div class="dofs-field dofs-field-small">
                            <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                            <select name="items[<?php echo $index; ?>][icon]">
                                <?php foreach ($icons as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($item['icon'] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="dofs-field dofs-field-checkbox">
                            <label>
                                <input type="checkbox" name="items[<?php echo $index; ?>][enabled]" value="1" <?php checked($item['enabled'] ?? true); ?>>
                                <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="dofs-item-actions">
                        <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="dofs-add-item-wrap">
                <button type="button" class="button" id="add-services-item">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e('Add Service', 'dofs-theme'); ?>
                </button>
            </div>

            <p class="submit">
                <input type="submit" name="dofs_save_services" class="button button-primary button-large" value="<?php esc_attr_e('Save Changes', 'dofs-theme'); ?>">
            </p>
        </form>
    </div>

    <script type="text/html" id="tmpl-services-item">
        <div class="dofs-item-row" data-index="{{data.index}}">
            <div class="dofs-item-handle">
                <span class="dashicons dashicons-menu"></span>
            </div>
            <div class="dofs-item-fields">
                <input type="hidden" name="items[{{data.index}}][id]" value="">

                <div class="dofs-field">
                    <label><?php esc_html_e('Title', 'dofs-theme'); ?></label>
                    <input type="text" name="items[{{data.index}}][title]" value="" required>
                </div>

                <div class="dofs-field">
                    <label><?php esc_html_e('URL', 'dofs-theme'); ?></label>
                    <input type="url" name="items[{{data.index}}][url]" value="" placeholder="https://" required>
                </div>

                <div class="dofs-field dofs-field-small">
                    <label><?php esc_html_e('Icon', 'dofs-theme'); ?></label>
                    <select name="items[{{data.index}}][icon]">
                        <?php foreach ($icons as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="dofs-field dofs-field-checkbox">
                    <label>
                        <input type="checkbox" name="items[{{data.index}}][enabled]" value="1" checked>
                        <?php esc_html_e('Enabled', 'dofs-theme'); ?>
                    </label>
                </div>
            </div>
            <div class="dofs-item-actions">
                <button type="button" class="button dofs-remove-item" title="<?php esc_attr_e('Remove', 'dofs-theme'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
    </script>
    <?php
}

/**
 * Get configured Quick Access items
 */
function dofs_get_configured_quick_access(): array {
    $items = get_option('dofs_quick_access_items', []);

    if (empty($items)) {
        // Return defaults
        return [
            ['id' => 'crm', 'title' => __('CRM', 'dofs-theme'), 'url' => home_url('/crm/'), 'icon' => 'users', 'gradient' => 'from-blue-500 to-blue-600', 'shadow' => 'shadow-blue-500/25'],
            ['id' => 'sales', 'title' => __('Sales & Orders', 'dofs-theme'), 'url' => home_url('/sales/'), 'icon' => 'chart', 'gradient' => 'from-purple-500 to-purple-600', 'shadow' => 'shadow-purple-500/25'],
            ['id' => 'production', 'title' => __('Production', 'dofs-theme'), 'url' => home_url('/production/'), 'icon' => 'factory', 'gradient' => 'from-orange-500 to-orange-600', 'shadow' => 'shadow-orange-500/25'],
            ['id' => 'warehouse', 'title' => __('Warehouse', 'dofs-theme'), 'url' => home_url('/warehouse/'), 'icon' => 'warehouse', 'gradient' => 'from-green-500 to-green-600', 'shadow' => 'shadow-green-500/25'],
            ['id' => 'projects', 'title' => __('Projects', 'dofs-theme'), 'url' => home_url('/projects/'), 'icon' => 'grid', 'gradient' => 'from-pink-500 to-pink-600', 'shadow' => 'shadow-pink-500/25'],
            ['id' => 'reports', 'title' => __('Reports', 'dofs-theme'), 'url' => home_url('/reports/'), 'icon' => 'chart-bar', 'gradient' => 'from-cyan-500 to-cyan-600', 'shadow' => 'shadow-cyan-500/25'],
        ];
    }

    // Filter enabled items and add shadow class
    $shadow_map = [
        'from-blue-500 to-blue-600' => 'shadow-blue-500/25',
        'from-purple-500 to-purple-600' => 'shadow-purple-500/25',
        'from-green-500 to-green-600' => 'shadow-green-500/25',
        'from-orange-500 to-orange-600' => 'shadow-orange-500/25',
        'from-pink-500 to-pink-600' => 'shadow-pink-500/25',
        'from-cyan-500 to-cyan-600' => 'shadow-cyan-500/25',
        'from-red-500 to-red-600' => 'shadow-red-500/25',
        'from-yellow-500 to-yellow-600' => 'shadow-yellow-500/25',
        'from-indigo-500 to-indigo-600' => 'shadow-indigo-500/25',
        'from-teal-500 to-teal-600' => 'shadow-teal-500/25',
    ];

    return array_filter(array_map(function($item) use ($shadow_map) {
        if (empty($item['enabled'])) return null;
        $item['shadow'] = $shadow_map[$item['gradient']] ?? 'shadow-gray-500/25';
        return $item;
    }, $items));
}

/**
 * Get configured Quick Actions items
 */
function dofs_get_configured_quick_actions(): array {
    $items = get_option('dofs_quick_actions_items', []);

    if (empty($items)) {
        // Return defaults
        return [
            ['title' => __('All Orders', 'dofs-theme'), 'url' => home_url('/sales/orders/'), 'icon' => 'chart'],
            ['title' => __('New Customer', 'dofs-theme'), 'url' => home_url('/crm/new-customer/'), 'icon' => 'users'],
            ['title' => __('New Entry', 'dofs-theme'), 'url' => home_url('/crm/new-entry/'), 'icon' => 'document'],
            ['title' => __('New Invoice', 'dofs-theme'), 'url' => home_url('/crm/new-invoice/'), 'icon' => 'cart'],
            ['title' => __('New Project', 'dofs-theme'), 'url' => home_url('/projects/new/'), 'icon' => 'grid'],
            ['title' => __('New Maintenance', 'dofs-theme'), 'url' => home_url('/maintenance/new/'), 'icon' => 'tool'],
            ['title' => __('View Reports', 'dofs-theme'), 'url' => home_url('/reports/'), 'icon' => 'chart-bar'],
        ];
    }

    // Filter enabled items
    return array_filter($items, function($item) {
        return !empty($item['enabled']);
    });
}

/**
 * Get configured Services items
 */
function dofs_get_configured_services(): array {
    $items = get_option('dofs_services_items', []);

    if (empty($items)) {
        // Return defaults
        return [
            ['title' => 'Google', 'url' => 'https://google.com', 'icon' => 'search'],
            ['title' => 'Gmail', 'url' => 'https://mail.google.com', 'icon' => 'mail'],
            ['title' => 'Calendar', 'url' => 'https://calendar.google.com', 'icon' => 'calendar'],
        ];
    }

    // Filter enabled items
    return array_filter($items, function($item) {
        return !empty($item['enabled']);
    });
}
