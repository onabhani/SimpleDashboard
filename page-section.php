<?php
/**
 * Template Name: Section Page
 * Template for section pages with sub-navigation bar (Odoo-style)
 *
 * @package DOFS_Theme
 */

defined('ABSPATH') || exit;

get_header();
get_sidebar();
?>

<!-- Main content area with sub-navigation -->
<main class="flex-1 overflow-y-auto flex flex-col">
    <?php
    // Render sub-navigation bar if in a section
    dofs_render_subnav();
    ?>

    <div class="flex-1 p-4 lg:p-6">
        <?php
        // Get current section info
        $section = dofs_get_current_section();
        ?>

        <!-- Page Header -->
        <div class="mb-6">
            <?php if ($section): ?>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                    <?php echo dofs_icon($section['icon'] ?? 'document', 'w-5 h-5'); ?>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?php echo esc_html($section['title']); ?>
                </h1>
            </div>
            <?php else: ?>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <?php the_title(); ?>
            </h1>
            <?php endif; ?>
        </div>

        <!-- Page Content -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <?php
            if (have_posts()):
                while (have_posts()):
                    the_post();
                    ?>
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        <?php the_content(); ?>
                    </div>
                    <?php
                endwhile;
            else:
                ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <?php echo dofs_icon('document', 'w-8 h-8 text-gray-400'); ?>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        <?php esc_html_e('Section Overview', 'dofs-theme'); ?>
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        <?php esc_html_e('This section page can display dynamic content from your plugins or custom code.', 'dofs-theme'); ?>
                    </p>
                </div>
                <?php
            endif;
            ?>
        </div>

        <?php
        /**
         * Hook: dofs_after_section_content
         * Use this hook to add dynamic content to section pages
         *
         * Example usage in a plugin:
         * add_action('dofs_after_section_content', function($section) {
         *     if ($section && $section['slug'] === 'crm') {
         *         // Render CRM specific content
         *     }
         * });
         */
        do_action('dofs_after_section_content', $section);
        ?>
    </div>
</main>

<?php
get_footer();
