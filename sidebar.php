<?php
/**
 * Sidebar Template
 *
 * @package DOFS_Theme
 */

$sidebar_menu = dofs_get_sidebar_menu();
?>

<!-- Sidebar backdrop (mobile) -->
<div
    id="sidebar-backdrop"
    class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden hidden"
    aria-hidden="true"
></div>

<!-- Sidebar -->
<aside
    id="sidebar"
    class="fixed inset-y-0 start-0 z-50 w-64 flex-shrink-0 bg-white dark:bg-gray-800 border-e border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-all duration-200 ease-in-out lg:relative lg:top-auto lg:h-auto lg:min-h-[calc(100vh-4rem)]"
>
    <div class="flex flex-col h-full pt-16 lg:pt-0">
        <!-- Mobile close button -->
        <div class="lg:hidden flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">D</span>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white sidebar-label">
                    <?php bloginfo('name'); ?>
                </span>
            </div>
            <button
                type="button"
                id="sidebar-close"
                class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                aria-label="<?php esc_attr_e('Close sidebar', 'dofs-theme'); ?>"
            >
                <?php echo dofs_icon('close', 'w-5 h-5'); ?>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
            <?php foreach ($sidebar_menu as $group): ?>
            <div>
                <h3 class="sidebar-section-title px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    <span class="sidebar-label"><?php echo esc_html($group['section']); ?></span>
                    <span class="sidebar-collapsed-indicator hidden" aria-hidden="true">
                        <span class="block w-5 h-px bg-gray-300 dark:bg-gray-600 mx-auto"></span>
                    </span>
                </h3>
                <ul class="space-y-1">
                    <?php foreach ($group['items'] as $item):
                        $is_current = !empty($item['current']);
                        $icon = $item['icon'] ?? 'document';
                    ?>
                    <li>
                        <a
                            href="<?php echo esc_url($item['url']); ?>"
                            target="<?php echo esc_attr($item['target'] ?? '_self'); ?>"
                            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 <?php echo $is_current
                                ? 'bg-gradient-to-r from-primary-500/10 to-primary-600/10 text-primary-700 dark:from-primary-500/20 dark:to-primary-600/20 dark:text-primary-400 border-s-2 border-primary-500'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700/50'; ?>"
                            <?php echo $is_current ? 'aria-current="page"' : ''; ?>
                            title="<?php echo esc_attr($item['title']); ?>"
                        >
                            <span class="flex-shrink-0 <?php echo $is_current ? 'text-primary-600 dark:text-primary-400' : ''; ?>">
                                <?php echo dofs_icon($icon, 'w-5 h-5'); ?>
                            </span>
                            <span class="sidebar-label"><?php echo esc_html($item['title']); ?></span>
                            <?php if (!empty($item['badge'])): ?>
                            <span class="sidebar-label ms-auto px-2 py-0.5 text-xs font-medium rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400">
                                <?php echo esc_html($item['badge']); ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </nav>

        <!-- Collapse toggle (desktop) -->
        <div class="hidden lg:block flex-shrink-0 px-3 py-2 border-t border-gray-200 dark:border-gray-700">
            <button
                type="button"
                id="sidebar-collapse-toggle"
                class="flex items-center justify-center w-full gap-2 px-3 py-2 rounded-xl text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors"
                aria-label="<?php esc_attr_e('Toggle sidebar', 'dofs-theme'); ?>"
            >
                <!-- Collapse icon (visible when expanded) -->
                <svg class="sidebar-collapse-icon w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
                <span class="sidebar-label"><?php esc_html_e('Collapse', 'dofs-theme'); ?></span>
                <!-- Expand icon (visible when collapsed) -->
                <svg class="sidebar-expand-icon w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <!-- Footer -->
        <div class="flex-shrink-0 p-3 lg:p-4 border-t border-gray-200 dark:border-gray-700 pb-safe">
            <div class="sidebar-footer-content flex items-center gap-2 lg:gap-3 p-2 lg:p-3 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-700/30">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-lg lg:rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg shadow-green-500/25">
                        <?php echo dofs_icon('tasks', 'w-4 h-4 lg:w-5 lg:h-5 text-white'); ?>
                    </div>
                </div>
                <div class="sidebar-label flex-1 min-w-0">
                    <p class="text-xs lg:text-sm font-medium text-gray-900 dark:text-white">
                        <?php esc_html_e('System Status', 'dofs-theme'); ?>
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <?php esc_html_e('Online', 'dofs-theme'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
