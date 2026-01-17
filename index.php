<?php
/**
 * Main Template
 *
 * @package DOFS_Theme
 */

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6">
        <?php if (have_posts()): ?>
            <?php if (is_home() && !is_front_page()): ?>
                <header class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?php single_post_title(); ?>
                    </h1>
                </header>
            <?php endif; ?>

            <div class="grid gap-6">
                <?php while (have_posts()): the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden'); ?>>
                        <?php if (has_post_thumbnail()): ?>
                            <div class="aspect-video overflow-hidden">
                                <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <header class="mb-4">
                                <?php the_title('<h2 class="text-xl font-semibold text-gray-900 dark:text-white"><a href="' . esc_url(get_permalink()) . '" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">', '</a></h2>'); ?>
                                <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </time>
                                    <span>&middot;</span>
                                    <span><?php echo esc_html(get_the_author()); ?></span>
                                </div>
                            </header>

                            <div class="prose prose-gray dark:prose-invert max-w-none">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="mt-4">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                                    <?php esc_html_e('Read more', 'dofs-theme'); ?>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            </footer>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <nav class="mt-8 flex items-center justify-center gap-2">
                <?php
                echo paginate_links([
                    'prev_text' => '&larr; ' . __('Previous', 'dofs-theme'),
                    'next_text' => __('Next', 'dofs-theme') . ' &rarr;',
                    'before_page_number' => '<span class="sr-only">' . __('Page', 'dofs-theme') . ' </span>',
                ]);
                ?>
            </nav>

        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <?php echo dofs_icon('document', 'w-8 h-8 text-gray-400'); ?>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    <?php esc_html_e('No content found', 'dofs-theme'); ?>
                </h2>
                <p class="text-gray-500 dark:text-gray-400">
                    <?php esc_html_e('There is no content to display at this time.', 'dofs-theme'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
