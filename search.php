<?php
/**
 * Search Results Template
 *
 * @package DOFS_Theme
 */

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6">
        <!-- Search header -->
        <header class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                <?php
                printf(
                    esc_html__('Search results for: %s', 'dofs-theme'),
                    '<span class="text-primary-600 dark:text-primary-400">' . esc_html(get_search_query()) . '</span>'
                );
                ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <?php
                global $wp_query;
                printf(
                    esc_html(_n('%d result found', '%d results found', $wp_query->found_posts, 'dofs-theme')),
                    $wp_query->found_posts
                );
                ?>
            </p>
        </header>

        <!-- Search form -->
        <div class="mb-6">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative max-w-xl">
                <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                    <?php echo dofs_icon('search', 'w-5 h-5 text-gray-400'); ?>
                </div>
                <input
                    type="search"
                    name="s"
                    value="<?php echo esc_attr(get_search_query()); ?>"
                    placeholder="<?php esc_attr_e('Search...', 'dofs-theme'); ?>"
                    class="block w-full ps-12 pe-4 py-3 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-500 dark:placeholder-gray-400 transition-shadow"
                >
            </form>
        </div>

        <?php if (have_posts()): ?>
            <div class="grid gap-4">
                <?php while (have_posts()): the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow'); ?>>
                        <div class="flex gap-4">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="flex-shrink-0 w-24 h-24 rounded-xl overflow-hidden">
                                    <?php the_post_thumbnail('thumbnail', ['class' => 'w-full h-full object-cover']); ?>
                                </a>
                            <?php endif; ?>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?>
                                    </span>
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </time>
                                </div>

                                <?php the_title('<h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate"><a href="' . esc_url(get_permalink()) . '" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">', '</a></h2>'); ?>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 25)); ?>
                                </p>
                            </div>
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
                ]);
                ?>
            </nav>

        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <?php echo dofs_icon('search', 'w-8 h-8 text-gray-400'); ?>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    <?php esc_html_e('No results found', 'dofs-theme'); ?>
                </h2>
                <p class="text-gray-500 dark:text-gray-400">
                    <?php esc_html_e('Try searching with different keywords.', 'dofs-theme'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
