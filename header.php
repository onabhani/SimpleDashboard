<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-gray-50 dark:bg-gray-900'); ?>>
<?php wp_body_open(); ?>

<div id="dofs-app" class="min-h-screen flex flex-col">
    <!-- Topbar -->
    <header class="sticky top-0 z-40 flex-shrink-0 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between h-full px-4 lg:px-6">
            <!-- Left section: Mobile menu button + Logo -->
            <div class="flex items-center gap-4">
                <!-- Mobile sidebar toggle -->
                <button
                    type="button"
                    id="sidebar-toggle"
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                    aria-label="<?php esc_attr_e('Toggle sidebar', 'dofs-theme'); ?>"
                >
                    <?php echo dofs_icon('menu', 'w-6 h-6'); ?>
                </button>

                <!-- Logo/Brand -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3">
                    <?php if (has_custom_logo()): ?>
                        <?php the_custom_logo(); ?>
                    <?php else: ?>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/25">
                            <span class="text-white font-bold text-lg">D</span>
                        </div>
                        <span class="hidden sm:block font-semibold text-lg text-gray-900 dark:text-white">
                            <?php bloginfo('name'); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Center section: Search -->
            <div class="hidden md:flex flex-1 max-w-xl mx-8">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <?php echo dofs_icon('search', 'w-5 h-5 text-gray-400'); ?>
                    </div>
                    <input
                        type="search"
                        id="topbar-search"
                        class="block w-full ps-10 pe-4 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 border-0 rounded-xl focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 placeholder-gray-500 dark:placeholder-gray-400 transition-shadow"
                        placeholder="<?php esc_attr_e('Search anything...', 'dofs-theme'); ?>"
                    >
                    <kbd class="absolute inset-y-0 end-3 flex items-center text-xs text-gray-400 font-sans">
                        <span class="hidden lg:inline px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600">⌘K</span>
                    </kbd>
                </div>
            </div>

            <!-- Right section: Actions + User -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Mobile search button -->
                <button
                    type="button"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                    aria-label="<?php esc_attr_e('Search', 'dofs-theme'); ?>"
                >
                    <?php echo dofs_icon('search', 'w-5 h-5'); ?>
                </button>

                <!-- Theme toggle -->
                <button
                    type="button"
                    id="theme-toggle"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                    aria-label="<?php esc_attr_e('Toggle theme', 'dofs-theme'); ?>"
                >
                    <span class="dark:hidden"><?php echo dofs_icon('moon', 'w-5 h-5'); ?></span>
                    <span class="hidden dark:inline"><?php echo dofs_icon('sun', 'w-5 h-5'); ?></span>
                </button>

                <!-- Notifications -->
                <div class="relative" id="notifications-container">
                    <button
                        type="button"
                        id="notifications-toggle"
                        class="relative p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                        aria-label="<?php esc_attr_e('Notifications', 'dofs-theme'); ?>"
                    >
                        <?php echo dofs_icon('bell', 'w-5 h-5'); ?>
                        <?php
                        /**
                         * Hook: dofs_notification_count
                         * Render notification badge count
                         */
                        do_action('dofs_notification_count');
                        ?>
                    </button>

                    <!-- Notifications dropdown -->
                    <div
                        id="notifications-dropdown"
                        class="hidden absolute end-0 mt-2 w-80 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50"
                    >
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                <?php esc_html_e('Notifications', 'dofs-theme'); ?>
                            </h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <?php
                            /**
                             * Hook: dofs_topbar_notifications
                             * Render notification items in dropdown
                             */
                            do_action('dofs_topbar_notifications');
                            ?>
                        </div>
                    </div>
                </div>

                <!-- User dropdown -->
                <?php if (is_user_logged_in()):
                    $current_user = wp_get_current_user();
                ?>
                <div class="relative" id="user-dropdown-container">
                    <button
                        type="button"
                        id="user-dropdown-toggle"
                        class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <img
                            src="<?php echo esc_url(get_avatar_url($current_user->ID, ['size' => 80])); ?>"
                            alt="<?php echo esc_attr($current_user->display_name); ?>"
                            class="w-8 h-8 rounded-lg object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                        >
                        <div class="hidden sm:block text-start">
                            <p class="text-sm font-medium text-gray-900 dark:text-white leading-tight">
                                <?php echo esc_html($current_user->display_name); ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight">
                                <?php echo esc_html(ucfirst($current_user->roles[0] ?? 'User')); ?>
                            </p>
                        </div>
                        <?php echo dofs_icon('chevron-down', 'hidden sm:block w-4 h-4 text-gray-400'); ?>
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        id="user-dropdown-menu"
                        class="hidden absolute end-0 mt-2 w-56 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 py-1 z-50"
                    >
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo esc_html($current_user->display_name); ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                <?php echo esc_html($current_user->user_email); ?>
                            </p>
                        </div>
                        <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <?php echo dofs_icon('user', 'w-4 h-4'); ?>
                            <?php esc_html_e('My Profile', 'dofs-theme'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/settings/')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <?php echo dofs_icon('settings', 'w-4 h-4'); ?>
                            <?php esc_html_e('Settings', 'dofs-theme'); ?>
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <?php echo dofs_icon('logout', 'w-4 h-4'); ?>
                            <?php esc_html_e('Sign Out', 'dofs-theme'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main layout with sidebar -->
    <div class="flex flex-1 overflow-hidden">
