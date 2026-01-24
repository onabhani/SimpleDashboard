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
                /**
                 * Get Quick Access items from admin settings
                 * Configure via Dashboard > DOFS Dashboard > Quick Access
                 */
                $quick_access = apply_filters('dofs_quick_access_items', dofs_get_configured_quick_access());

                // Filter out hidden items based on user settings
                $hidden_items = get_user_meta(get_current_user_id(), 'dofs_quick_access_hidden', true);
                $hidden_items = is_array($hidden_items) ? $hidden_items : [];

                $quick_access = array_filter($quick_access, function($item) use ($hidden_items) {
                    $item_id = $item['id'] ?? sanitize_title($item['title']);
                    return !in_array($item_id, $hidden_items);
                });

                foreach ($quick_access as $item):
                ?>
                <a href="<?php echo esc_url($item['url']); ?>" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br <?php echo esc_attr($item['gradient']); ?> p-4 sm:p-5 text-white shadow-lg <?php echo esc_attr($item['shadow']); ?> hover:scale-105 transition-transform duration-200 min-h-[100px] sm:min-h-[120px] lg:aspect-square flex flex-col justify-between">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-white/20 flex items-center justify-center mb-2 sm:mb-3">
                            <?php echo dofs_icon($item['icon'], 'w-5 h-5 sm:w-6 sm:h-6'); ?>
                        </div>
                        <h3 class="font-medium sm:font-semibold text-sm sm:text-base"><?php echo esc_html($item['title']); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Quick Actions -->
        <section>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <?php esc_html_e('Quick Actions', 'dofs-theme'); ?>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:flex lg:flex-wrap gap-2 sm:gap-3">
                <?php
                /**
                 * Get Quick Actions from admin settings
                 * Configure via Dashboard > DOFS Dashboard > Quick Actions
                 */
                $quick_actions = apply_filters('dofs_quick_actions', dofs_get_configured_quick_actions());

                foreach ($quick_actions as $action):
                ?>
                <a href="<?php echo esc_url($action['url']); ?>" class="flex items-center justify-center gap-2 px-3 py-2.5 sm:px-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-primary-300 dark:hover:border-primary-600 transition-colors">
                    <?php echo dofs_icon($action['icon'], 'w-4 h-4 text-gray-400 shrink-0'); ?>
                    <span class="truncate"><?php echo esc_html($action['title']); ?></span>
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
            <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 min-w-0">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?php esc_html_e('Recent Orders', 'dofs-theme'); ?>
                    </h2>
                    <a href="<?php echo esc_url(home_url('/orders/')); ?>" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                        <?php esc_html_e('View all', 'dofs-theme'); ?>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[500px]">
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
            <?php
            /**
             * Get workflow entries assigned to current user
             * This fetches actual entries from Gravity Forms where user is assigned
             *
             * Configure form fields with admin labels:
             * - "assignee" or "assigned" - User assigned to the task
             * - "status" or "workflow" - Current status/step
             * - "priority" - Task priority (high, medium, low)
             * - "due" or "deadline" - Due date
             * - "title" or "subject" - Task title/name
             */
            $workflow_tasks = dofs_get_user_workflow_tasks(null, 5);
            $pending_count = count(array_filter($workflow_tasks, function($task) {
                return !in_array($task['status'], ['complete', 'completed', 'done', 'closed']);
            }));

            $priority_classes = [
                'high' => 'bg-red-500',
                'medium' => 'bg-yellow-500',
                'low' => 'bg-gray-400',
            ];
            ?>
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 min-w-0 overflow-hidden">
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?php esc_html_e('My Tasks', 'dofs-theme'); ?>
                    </h2>
                    <?php if ($pending_count > 0): ?>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">
                        <?php echo esc_html($pending_count); ?> <?php esc_html_e('pending', 'dofs-theme'); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    <?php if (!empty($workflow_tasks)): ?>
                        <?php foreach ($workflow_tasks as $task): ?>
                        <a href="<?php echo esc_url($task['url']); ?>" class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group">
                            <div class="flex-shrink-0 mt-1.5">
                                <div class="w-2 h-2 rounded-full <?php echo esc_attr($priority_classes[$task['priority']] ?? 'bg-gray-400'); ?>"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    <?php echo esc_html($task['title']); ?>
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php if (!empty($task['due_label'])): ?>
                                            <?php esc_html_e('Due:', 'dofs-theme'); ?> <?php echo esc_html($task['due_label']); ?>
                                        <?php else: ?>
                                            <?php echo esc_html($task['form_title']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($task['status'] && $task['status'] !== 'pending'): ?>
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        <?php echo esc_html(ucfirst($task['status'])); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php echo dofs_icon('chevron-down', 'w-4 h-4 text-gray-400 -rotate-90'); ?>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <?php echo dofs_icon('tasks', 'w-6 h-6 text-gray-400'); ?>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <?php esc_html_e('No tasks assigned to you', 'dofs-theme'); ?>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                <?php esc_html_e('Tasks will appear here when assigned', 'dofs-theme'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-4 sm:p-5 pt-0">
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
