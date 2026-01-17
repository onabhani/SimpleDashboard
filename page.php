<?php
/**
 * Page Template
 *
 * @package DOFS_Theme
 */

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6">
        <?php while (have_posts()): the_post(); ?>
            <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                <!-- Page header -->
                <header class="mb-6">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                        <?php the_title(); ?>
                    </h1>
                    <?php if (has_excerpt()): ?>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                            <?php echo esc_html(get_the_excerpt()); ?>
                        </p>
                    <?php endif; ?>
                </header>

                <!-- Page content -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="aspect-[3/1] overflow-hidden rounded-t-2xl">
                            <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-6 lg:p-8">
                        <div class="prose prose-gray dark:prose-invert max-w-none prose-headings:font-semibold prose-a:text-primary-600 dark:prose-a:text-primary-400">
                            <?php the_content(); ?>
                        </div>

                        <?php
                        wp_link_pages([
                            'before' => '<nav class="page-links mt-6 pt-6 border-t border-gray-200 dark:border-gray-700"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">' . __('Pages:', 'dofs-theme') . '</span>',
                            'after' => '</nav>',
                            'link_before' => '<span class="px-3 py-1 mx-1 text-sm rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">',
                            'link_after' => '</span>',
                        ]);
                        ?>
                    </div>
                </div>

                <?php if (comments_open() || get_comments_number()): ?>
                    <section class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                        <?php comments_template(); ?>
                    </section>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>

<?php get_footer(); ?>
