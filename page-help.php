<?php
/**
 * Template Name: Help
 * Template for the Help page - displays content from WordPress page
 *
 * @package DOFS_Theme
 */

defined('ABSPATH') || exit;

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?php the_title(); ?>
                </h1>
                <?php if (has_excerpt()): ?>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    <?php the_excerpt(); ?>
                </p>
                <?php endif; ?>
            </div>

            <!-- Help Content -->
            <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 lg:p-8 prose prose-gray dark:prose-invert max-w-none
                    prose-headings:text-gray-900 dark:prose-headings:text-white
                    prose-h2:text-xl prose-h2:font-semibold prose-h2:mt-8 prose-h2:mb-4
                    prose-h3:text-lg prose-h3:font-medium prose-h3:mt-6 prose-h3:mb-3
                    prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                    prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-a:no-underline hover:prose-a:underline
                    prose-ul:text-gray-600 dark:prose-ul:text-gray-300
                    prose-ol:text-gray-600 dark:prose-ol:text-gray-300
                    prose-li:my-1
                    prose-strong:text-gray-900 dark:prose-strong:text-white
                    prose-code:text-primary-600 dark:prose-code:text-primary-400 prose-code:bg-gray-100 dark:prose-code:bg-gray-700 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                    prose-pre:bg-gray-900 dark:prose-pre:bg-gray-950 prose-pre:rounded-xl
                    prose-img:rounded-xl prose-img:shadow-md
                    prose-hr:border-gray-200 dark:prose-hr:border-gray-700
                ">
                    <?php
                    while (have_posts()):
                        the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
            </article>

            <!-- Last Updated -->
            <?php if (get_the_modified_time('U') > get_the_time('U')): ?>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                <?php
                printf(
                    esc_html__('Last updated: %s', 'dofs-theme'),
                    get_the_modified_date()
                );
                ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
