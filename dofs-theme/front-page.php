<?php
/**
 * Front Page Template - Dashboard Home
 *
 * @package DOFS_Theme
 */

get_header();
get_sidebar();
?>

<!-- Main content area -->
<main class="flex-1 overflow-y-auto">
    <div class="p-4 lg:p-6 space-y-6">
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    <?php
                    $current_user = wp_get_current_user();
                    $hour = (int) date('H');
                    $greeting = $hour < 12 ? __('Good Morning', 'dofs-theme') : ($hour < 17 ? __('Good Afternoon', 'dofs-theme') : __('Good Evening', 'dofs-theme'));
                    printf('%s, %s!', esc_html($greeting), esc_html($current_user->display_name));
                    ?>
                </h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    <?php esc_html_e("Here's what's happening with your business today.", 'dofs-theme'); ?>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <?php echo esc_html(date_i18n(get_option('date_format') . ' - ' . get_option('time_format'))); ?>
                </span>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <section>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <?php esc_html_e('Quick Access', 'dofs-theme'); ?>
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php
                $quick_access = [
                    ['title' => __('Sales', 'dofs-theme'), 'icon' => 'chart', 'url' => home_url('/sales/'), 'gradient' => 'from-blue-500 to-blue-600', 'shadow' => 'shadow-blue-500/25'],
                    ['title' => __('Orders', 'dofs-theme'), 'icon' => 'cart', 'url' => home_url('/orders/'), 'gradient' => 'from-purple-500 to-purple-600', 'shadow' => 'shadow-purple-500/25'],
                    ['title' => __('HR', 'dofs-theme'), 'icon' => 'team', 'url' => home_url('/hr/'), 'gradient' => 'from-green-500 to-green-600', 'shadow' => 'shadow-green-500/25'],
                    ['title' => __('Reports', 'dofs-theme'), 'icon' => 'document', 'url' => home_url('/reports/'), 'gradient' => 'from-orange-500 to-orange-600', 'shadow' => 'shadow-orange-500/25'],
                    ['title' => __('Inventory', 'dofs-theme'), 'icon' => 'cube', 'url' => home_url('/products/'), 'gradient' => 'from-pink-500 to-pink-600', 'shadow' => 'shadow-pink-500/25'],
                    ['title' => __('Customers', 'dofs-theme'), 'icon' => 'users', 'url' => home_url('/customers/'), 'gradient' => 'from-cyan-500 to-cyan-600', 'shadow' => 'shadow-cyan-500/25'],
                ];

                foreach ($quick_access as $item):
                ?>
                <a href="<?php echo esc_url($item['url']); ?>" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br <?php echo esc_attr($item['gradient']); ?> p-5 text-white shadow-lg <?php echo esc_attr($item['shadow']); ?> hover:scale-105 transition-transform duration-200">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                            <?php echo dofs_icon($item['icon'], 'w-6 h-6'); ?>
                        </div>
                        <h3 class="font-semibold"><?php echo esc_html($item['title']); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Stats Row -->
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php
                $stats = [
                    ['label' => __('Total Revenue', 'dofs-theme'), 'value' => '$124,563', 'change' => '+12.5%', 'positive' => true, 'icon' => 'trending'],
                    ['label' => __('Orders Today', 'dofs-theme'), 'value' => '156', 'change' => '+8.2%', 'positive' => true, 'icon' => 'cart'],
                    ['label' => __('Active Employees', 'dofs-theme'), 'value' => '48', 'change' => '2 on leave', 'positive' => null, 'icon' => 'team'],
                    ['label' => __('Pending Tasks', 'dofs-theme'), 'value' => '23', 'change' => '5 urgent', 'positive' => false, 'icon' => 'tasks'],
                ];

                foreach ($stats as $stat):
                ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                <?php echo esc_html($stat['label']); ?>
                            </p>
                            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                <?php echo esc_html($stat['value']); ?>
                            </p>
                            <p class="mt-1 text-sm <?php echo $stat['positive'] === true ? 'text-green-600 dark:text-green-400' : ($stat['positive'] === false ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'); ?>">
                                <?php echo esc_html($stat['change']); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <?php echo dofs_icon($stat['icon'], 'w-6 h-6'); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Two Column Layout -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Recent Orders -->
            <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?php esc_html_e('Recent Orders', 'dofs-theme'); ?>
                    </h2>
                    <a href="<?php echo esc_url(home_url('/orders/')); ?>" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                        <?php esc_html_e('View all', 'dofs-theme'); ?>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3"><?php esc_html_e('Order', 'dofs-theme'); ?></th>
                                <th class="px-5 py-3"><?php esc_html_e('Customer', 'dofs-theme'); ?></th>
                                <th class="px-5 py-3"><?php esc_html_e('Amount', 'dofs-theme'); ?></th>
                                <th class="px-5 py-3"><?php esc_html_e('Status', 'dofs-theme'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php
                            $orders = [
                                ['id' => '#ORD-2024-001', 'customer' => 'John Smith', 'amount' => '$234.50', 'status' => 'completed', 'status_label' => __('Completed', 'dofs-theme')],
                                ['id' => '#ORD-2024-002', 'customer' => 'Sarah Johnson', 'amount' => '$89.00', 'status' => 'processing', 'status_label' => __('Processing', 'dofs-theme')],
                                ['id' => '#ORD-2024-003', 'customer' => 'Mike Wilson', 'amount' => '$567.00', 'status' => 'pending', 'status_label' => __('Pending', 'dofs-theme')],
                                ['id' => '#ORD-2024-004', 'customer' => 'Emily Davis', 'amount' => '$123.50', 'status' => 'completed', 'status_label' => __('Completed', 'dofs-theme')],
                                ['id' => '#ORD-2024-005', 'customer' => 'Chris Brown', 'amount' => '$445.00', 'status' => 'processing', 'status_label' => __('Processing', 'dofs-theme')],
                            ];

                            $status_classes = [
                                'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                                'processing' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                            ];

                            foreach ($orders as $order):
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo esc_html($order['id']); ?>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo esc_html($order['customer']); ?>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo esc_html($order['amount']); ?>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-lg <?php echo esc_attr($status_classes[$order['status']]); ?>">
                                        <?php echo esc_html($order['status_label']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- My Tasks -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?php esc_html_e('My Tasks', 'dofs-theme'); ?>
                    </h2>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">
                        5 <?php esc_html_e('pending', 'dofs-theme'); ?>
                    </span>
                </div>
                <div class="p-5 space-y-3">
                    <?php
                    $tasks = [
                        ['title' => __('Review leave request - Ahmed', 'dofs-theme'), 'priority' => 'high', 'due' => __('Today', 'dofs-theme')],
                        ['title' => __('Approve purchase order #PO-234', 'dofs-theme'), 'priority' => 'medium', 'due' => __('Tomorrow', 'dofs-theme')],
                        ['title' => __('Monthly sales report review', 'dofs-theme'), 'priority' => 'low', 'due' => __('This week', 'dofs-theme')],
                        ['title' => __('Team performance reviews', 'dofs-theme'), 'priority' => 'medium', 'due' => __('Jan 20', 'dofs-theme')],
                        ['title' => __('Inventory audit preparation', 'dofs-theme'), 'priority' => 'low', 'due' => __('Jan 25', 'dofs-theme')],
                    ];

                    $priority_classes = [
                        'high' => 'bg-red-500',
                        'medium' => 'bg-yellow-500',
                        'low' => 'bg-gray-400',
                    ];

                    foreach ($tasks as $task):
                    ?>
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 rounded-full <?php echo esc_attr($priority_classes[$task['priority']]); ?>"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo esc_html($task['title']); ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <?php esc_html_e('Due:', 'dofs-theme'); ?> <?php echo esc_html($task['due']); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="p-5 pt-0">
                    <a href="<?php echo esc_url(home_url('/tasks/')); ?>" class="block w-full py-2.5 text-center text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-xl hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                        <?php esc_html_e('View All Tasks', 'dofs-theme'); ?>
                    </a>
                </div>
            </section>
        </div>

        <!-- WordPress Content (if any) -->
        <?php if (have_posts()): ?>
            <?php while (have_posts()): the_post(); ?>
                <?php if (get_the_content()): ?>
                    <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="prose prose-gray dark:prose-invert max-w-none">
                            <?php the_content(); ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>
