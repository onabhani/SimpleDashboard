<?php
/**
 * 404 Template
 *
 * @package DOFS_Theme
 */

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6 flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-md">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 flex items-center justify-center">
                <span class="text-4xl font-bold text-primary-600 dark:text-primary-400">404</span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                <?php esc_html_e('Page not found', 'dofs-theme'); ?>
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                <?php esc_html_e("Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.", 'dofs-theme'); ?>
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                    <?php echo dofs_icon('home', 'w-5 h-5'); ?>
                    <?php esc_html_e('Go to Dashboard', 'dofs-theme'); ?>
                </a>

                <button onclick="history.back()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <?php esc_html_e('Go Back', 'dofs-theme'); ?>
                </button>
            </div>
        </div>
    </div>

<?php get_footer(); ?>
