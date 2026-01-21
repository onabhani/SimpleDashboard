<?php
/**
 * Template Name: Settings
 * Template for the user settings page
 *
 * @package DOFS_Theme
 */

defined('ABSPATH') || exit;

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();

// Get user settings (stored as user meta)
$user_settings = [
    'theme' => get_user_meta($current_user->ID, 'dofs_theme_preference', true) ?: 'system',
    'language' => get_user_meta($current_user->ID, 'dofs_language', true) ?: get_locale(),
    'date_format' => get_user_meta($current_user->ID, 'dofs_date_format', true) ?: get_option('date_format'),
    'time_format' => get_user_meta($current_user->ID, 'dofs_time_format', true) ?: get_option('time_format'),
    'timezone' => get_user_meta($current_user->ID, 'dofs_timezone', true) ?: wp_timezone_string(),
    'currency' => get_user_meta($current_user->ID, 'dofs_currency', true) ?: 'SAR',
    'number_format' => get_user_meta($current_user->ID, 'dofs_number_format', true) ?: 'en',
    'notifications_email' => get_user_meta($current_user->ID, 'dofs_notifications_email', true) !== '0',
    'notifications_push' => get_user_meta($current_user->ID, 'dofs_notifications_push', true) !== '0',
    'notifications_frequency' => get_user_meta($current_user->ID, 'dofs_notifications_frequency', true) ?: 'instant',
    'quick_access_hidden' => get_user_meta($current_user->ID, 'dofs_quick_access_hidden', true) ?: [],
];

get_header();
get_sidebar();
?>

<main class="flex-1 overflow-y-auto">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <?php esc_html_e('Settings', 'dofs-theme'); ?>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                <?php esc_html_e('Manage your account preferences and settings', 'dofs-theme'); ?>
            </p>
        </div>

        <!-- Settings Form -->
        <form id="settings-form" method="post" class="space-y-6">
            <?php wp_nonce_field('dofs_save_settings', 'dofs_settings_nonce'); ?>

            <!-- Display Preferences -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <?php echo dofs_icon('sun', 'w-5 h-5 text-amber-500'); ?>
                        <?php esc_html_e('Display Preferences', 'dofs-theme'); ?>
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Theme -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 pt-2">
                            <?php esc_html_e('Theme', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <div class="flex flex-wrap gap-3">
                                <label class="relative flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all <?php echo $user_settings['theme'] === 'light' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'; ?>">
                                    <input type="radio" name="theme" value="light" <?php checked($user_settings['theme'], 'light'); ?> class="sr-only peer">
                                    <?php echo dofs_icon('sun', 'w-5 h-5 text-amber-500'); ?>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php esc_html_e('Light', 'dofs-theme'); ?></span>
                                </label>
                                <label class="relative flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all <?php echo $user_settings['theme'] === 'dark' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'; ?>">
                                    <input type="radio" name="theme" value="dark" <?php checked($user_settings['theme'], 'dark'); ?> class="sr-only peer">
                                    <?php echo dofs_icon('moon', 'w-5 h-5 text-indigo-500'); ?>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php esc_html_e('Dark', 'dofs-theme'); ?></span>
                                </label>
                                <label class="relative flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all <?php echo $user_settings['theme'] === 'system' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'; ?>">
                                    <input type="radio" name="theme" value="system" <?php checked($user_settings['theme'], 'system'); ?> class="sr-only peer">
                                    <?php echo dofs_icon('settings', 'w-5 h-5 text-gray-500'); ?>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php esc_html_e('System', 'dofs-theme'); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="language" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Language', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="language" id="language" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="en_US" <?php selected($user_settings['language'], 'en_US'); ?>>English</option>
                                <option value="ar" <?php selected($user_settings['language'], 'ar'); ?>>العربية</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date Format -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="date_format" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Date Format', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="date_format" id="date_format" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="d/m/Y" <?php selected($user_settings['date_format'], 'd/m/Y'); ?>><?php echo date('d/m/Y'); ?> (DD/MM/YYYY)</option>
                                <option value="m/d/Y" <?php selected($user_settings['date_format'], 'm/d/Y'); ?>><?php echo date('m/d/Y'); ?> (MM/DD/YYYY)</option>
                                <option value="Y-m-d" <?php selected($user_settings['date_format'], 'Y-m-d'); ?>><?php echo date('Y-m-d'); ?> (YYYY-MM-DD)</option>
                                <option value="F j, Y" <?php selected($user_settings['date_format'], 'F j, Y'); ?>><?php echo date('F j, Y'); ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Time Format -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="time_format" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Time Format', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="time_format" id="time_format" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="g:i a" <?php selected($user_settings['time_format'], 'g:i a'); ?>><?php echo date('g:i a'); ?> (12-hour)</option>
                                <option value="H:i" <?php selected($user_settings['time_format'], 'H:i'); ?>><?php echo date('H:i'); ?> (24-hour)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notification Settings -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <?php echo dofs_icon('bell', 'w-5 h-5 text-red-500'); ?>
                        <?php esc_html_e('Notification Settings', 'dofs-theme'); ?>
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Email Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="notifications_email" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <?php esc_html_e('Email Notifications', 'dofs-theme'); ?>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?php esc_html_e('Receive notifications via email', 'dofs-theme'); ?>
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notifications_email" id="notifications_email" value="1" <?php checked($user_settings['notifications_email']); ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        </label>
                    </div>

                    <!-- Push Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="notifications_push" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <?php esc_html_e('Push Notifications', 'dofs-theme'); ?>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?php esc_html_e('Receive browser push notifications', 'dofs-theme'); ?>
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notifications_push" id="notifications_push" value="1" <?php checked($user_settings['notifications_push']); ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        </label>
                    </div>

                    <!-- Notification Frequency -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="notifications_frequency" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Email Frequency', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="notifications_frequency" id="notifications_frequency" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="instant" <?php selected($user_settings['notifications_frequency'], 'instant'); ?>><?php esc_html_e('Instant', 'dofs-theme'); ?></option>
                                <option value="daily" <?php selected($user_settings['notifications_frequency'], 'daily'); ?>><?php esc_html_e('Daily Digest', 'dofs-theme'); ?></option>
                                <option value="weekly" <?php selected($user_settings['notifications_frequency'], 'weekly'); ?>><?php esc_html_e('Weekly Digest', 'dofs-theme'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Privacy & Security -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <?php esc_html_e('Privacy & Security', 'dofs-theme'); ?>
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Change Password -->
                    <a href="<?php echo esc_url(admin_url('profile.php#password')); ?>" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php esc_html_e('Change Password', 'dofs-theme'); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('Update your account password', 'dofs-theme'); ?></p>
                            </div>
                        </div>
                        <?php echo dofs_icon('chevron-down', 'w-5 h-5 text-gray-400 -rotate-90 group-hover:translate-x-1 transition-transform'); ?>
                    </a>

                    <!-- Two-Factor Authentication -->
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php esc_html_e('Two-Factor Authentication', 'dofs-theme'); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('Add an extra layer of security', 'dofs-theme'); ?></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            <?php esc_html_e('Coming Soon', 'dofs-theme'); ?>
                        </span>
                    </div>

                    <!-- Active Sessions -->
                    <a href="#active-sessions" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group" id="view-sessions-btn">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php esc_html_e('Active Sessions', 'dofs-theme'); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('Manage your active login sessions', 'dofs-theme'); ?></p>
                            </div>
                        </div>
                        <?php echo dofs_icon('chevron-down', 'w-5 h-5 text-gray-400 -rotate-90 group-hover:translate-x-1 transition-transform'); ?>
                    </a>

                    <!-- Activity Log -->
                    <a href="<?php echo esc_url(home_url('/activity-log/')); ?>" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php esc_html_e('Activity Log', 'dofs-theme'); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('View your recent account activity', 'dofs-theme'); ?></p>
                            </div>
                        </div>
                        <?php echo dofs_icon('chevron-down', 'w-5 h-5 text-gray-400 -rotate-90 group-hover:translate-x-1 transition-transform'); ?>
                    </a>
                </div>
            </section>

            <!-- Regional Settings -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <?php esc_html_e('Regional Settings', 'dofs-theme'); ?>
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Timezone -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="timezone" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Timezone', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="timezone" id="timezone" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <?php
                                $timezones = timezone_identifiers_list();
                                foreach ($timezones as $tz) {
                                    $selected = $user_settings['timezone'] === $tz ? 'selected' : '';
                                    echo "<option value=\"{$tz}\" {$selected}>{$tz}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="currency" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Currency Display', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="currency" id="currency" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="SAR" <?php selected($user_settings['currency'], 'SAR'); ?>>SAR - Saudi Riyal (ر.س)</option>
                                <option value="USD" <?php selected($user_settings['currency'], 'USD'); ?>>USD - US Dollar ($)</option>
                                <option value="EUR" <?php selected($user_settings['currency'], 'EUR'); ?>>EUR - Euro (&#8364;)</option>
                                <option value="GBP" <?php selected($user_settings['currency'], 'GBP'); ?>>GBP - British Pound (&#163;)</option>
                                <option value="AED" <?php selected($user_settings['currency'], 'AED'); ?>>AED - UAE Dirham (د.إ)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Number Format -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <label for="number_format" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?php esc_html_e('Number Format', 'dofs-theme'); ?>
                        </label>
                        <div class="sm:col-span-2">
                            <select name="number_format" id="number_format" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="en" <?php selected($user_settings['number_format'], 'en'); ?>>1,234.56 (English)</option>
                                <option value="de" <?php selected($user_settings['number_format'], 'de'); ?>>1.234,56 (German/Arabic)</option>
                                <option value="fr" <?php selected($user_settings['number_format'], 'fr'); ?>>1 234,56 (French)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Access Customization -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <?php echo dofs_icon('grid', 'w-5 h-5 text-pink-500'); ?>
                        <?php esc_html_e('Quick Access Customization', 'dofs-theme'); ?>
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <?php esc_html_e('Choose which cards to show on your dashboard Quick Access section.', 'dofs-theme'); ?>
                    </p>
                    <?php
                    $quick_access_items = apply_filters('dofs_quick_access_items', [
                        ['id' => 'sales', 'title' => __('Sales', 'dofs-theme')],
                        ['id' => 'orders', 'title' => __('Orders', 'dofs-theme')],
                        ['id' => 'hr', 'title' => __('HR', 'dofs-theme')],
                        ['id' => 'reports', 'title' => __('Reports', 'dofs-theme')],
                        ['id' => 'inventory', 'title' => __('Inventory', 'dofs-theme')],
                        ['id' => 'customers', 'title' => __('Customers', 'dofs-theme')],
                    ]);
                    $hidden_items = is_array($user_settings['quick_access_hidden']) ? $user_settings['quick_access_hidden'] : [];
                    ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach ($quick_access_items as $item): ?>
                            <?php
                            $item_id = $item['id'] ?? sanitize_title($item['title']);
                            $is_visible = !in_array($item_id, $hidden_items);
                            ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all <?php echo $is_visible ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600'; ?>">
                                <input type="checkbox" name="quick_access_visible[]" value="<?php echo esc_attr($item_id); ?>" <?php checked($is_visible); ?> class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo esc_html($item['title']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Save Button -->
            <div class="flex justify-end gap-3">
                <button type="button" onclick="window.location.reload()" class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <?php esc_html_e('Cancel', 'dofs-theme'); ?>
                </button>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-xl hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 transition-colors">
                    <?php esc_html_e('Save Changes', 'dofs-theme'); ?>
                </button>
            </div>
        </form>

        <!-- Success Message (hidden by default) -->
        <div id="settings-success" class="hidden fixed bottom-6 right-6 px-6 py-4 bg-green-500 text-white rounded-xl shadow-lg flex items-center gap-3 z-50">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span><?php esc_html_e('Settings saved successfully!', 'dofs-theme'); ?></span>
        </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settings-form');
    const successMsg = document.getElementById('settings-success');
    const ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

    if (!form) return;

    // Handle theme radio changes for visual feedback
    form.querySelectorAll('input[name="theme"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            form.querySelectorAll('input[name="theme"]').forEach(function(r) {
                const label = r.closest('label');
                if (r.checked) {
                    label.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    label.classList.remove('border-gray-200', 'dark:border-gray-600');
                } else {
                    label.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    label.classList.add('border-gray-200', 'dark:border-gray-600');
                }
            });
        });
    });

    // Handle quick access checkbox changes for visual feedback
    form.querySelectorAll('input[name="quick_access_visible[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                label.classList.remove('border-gray-200', 'dark:border-gray-600');
            } else {
                label.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                label.classList.add('border-gray-200', 'dark:border-gray-600');
            }
        });
    });

    // Form submission via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = '<?php echo esc_js(__('Saving...', 'dofs-theme')); ?>';
        submitBtn.disabled = true;

        const formData = new FormData(form);
        formData.append('action', 'dofs_save_settings');

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;

            if (data.success) {
                // Show success message
                successMsg.classList.remove('hidden');
                setTimeout(function() {
                    successMsg.classList.add('hidden');
                }, 3000);

                // Apply theme immediately if changed
                const themeValue = formData.get('theme');
                if (themeValue) {
                    applyTheme(themeValue);
                }
            } else {
                alert(data.data || '<?php echo esc_js(__('Failed to save settings', 'dofs-theme')); ?>');
            }
        })
        .catch(function(error) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            console.error('Error:', error);
            alert('<?php echo esc_js(__('An error occurred while saving settings', 'dofs-theme')); ?>');
        });
    });

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('dofs-theme', 'dark');
        } else if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('dofs-theme', 'light');
        } else {
            localStorage.removeItem('dofs-theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
});
</script>

<?php
get_footer();
