<?php
/**
 * Single Post Template
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
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <!-- Post header -->
                <header class="mb-6">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                        <?php
                        $categories = get_the_category();
                        if ($categories):
                            foreach ($categories as $category):
                        ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="px-2.5 py-1 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                        <?php the_title(); ?>
                    </h1>

                    <div class="mt-4 flex items-center gap-4">
                        <img
                            src="<?php echo esc_url(get_avatar_url(get_the_author_meta('ID'), ['size' => 80])); ?>"
                            alt="<?php echo esc_attr(get_the_author()); ?>"
                            class="w-10 h-10 rounded-full ring-2 ring-gray-200 dark:ring-gray-600"
                        >
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php the_author(); ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                                &middot;
                                <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('ago', 'dofs-theme')); ?>
                            </p>
                        </div>
                    </div>
                </header>

                <!-- Post content -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="aspect-video overflow-hidden rounded-t-2xl">
                            <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-6 lg:p-8">
                        <div class="prose prose-gray dark:prose-invert max-w-none prose-headings:font-semibold prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-img:rounded-xl">
                            <?php the_content(); ?>
                        </div>

                        <?php
                        wp_link_pages([
                            'before' => '<nav class="page-links mt-6 pt-6 border-t border-gray-200 dark:border-gray-700"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">' . __('Pages:', 'dofs-theme') . '</span>',
                            'after' => '</nav>',
                        ]);
                        ?>

                        <!-- Tags -->
                        <?php
                        $tags = get_the_tags();
                        if ($tags):
                        ?>
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php esc_html_e('Tags:', 'dofs-theme'); ?></span>
                                <?php foreach ($tags as $tag): ?>
                                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="px-2.5 py-1 text-sm rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        #<?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Post navigation -->
                <nav class="mt-6 grid sm:grid-cols-2 gap-4">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>

                    <?php if ($prev_post): ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="group flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center group-hover:bg-primary-100 dark:group-hover:bg-primary-900/30 transition-colors">
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-primary-600 dark:group-hover:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('Previous', 'dofs-theme'); ?></p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    <?php echo esc_html(get_the_title($prev_post)); ?>
                                </p>
                            </div>
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($next_post): ?>
                        <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="group flex items-center justify-end gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 transition-colors text-end">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php esc_html_e('Next', 'dofs-theme'); ?></p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    <?php echo esc_html(get_the_title($next_post)); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center group-hover:bg-primary-100 dark:group-hover:bg-primary-900/30 transition-colors">
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-primary-600 dark:group-hover:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    <?php endif; ?>
                </nav>

                <?php if (comments_open() || get_comments_number()): ?>
                    <section class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                        <?php comments_template(); ?>
                    </section>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>

<?php get_footer(); ?>
