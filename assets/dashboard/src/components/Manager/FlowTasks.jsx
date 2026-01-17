import React from 'react';
import { useFlowTasks } from '../../hooks/useFlowTasks';

/**
 * GravityFlow tasks component
 */
export default function FlowTasks() {
    const { data, loading, error } = useFlowTasks();

    const tasks = data?.tasks || [];

    return (
        <div className="dashboard-card">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                        My Workflows
                    </h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Non-HR process tasks from GravityFlow
                    </p>
                </div>
                {tasks.length > 0 && (
                    <span className="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        {tasks.length} pending
                    </span>
                )}
            </div>

            {/* Loading state */}
            {loading && (
                <div className="space-y-3">
                    {[1, 2, 3].map((i) => (
                        <div key={i} className="flex items-center gap-4">
                            <div className="flex-1 space-y-2">
                                <div className="skeleton h-4 w-32" />
                                <div className="skeleton h-3 w-48" />
                            </div>
                            <div className="skeleton h-8 w-16" />
                        </div>
                    ))}
                </div>
            )}

            {/* Error state */}
            {error && (
                <div className="empty-state">
                    <p className="text-red-600 dark:text-red-400">
                        Failed to load workflow tasks. Please refresh the page.
                    </p>
                </div>
            )}

            {/* Empty state */}
            {!loading && !error && tasks.length === 0 && (
                <div className="empty-state">
                    <CheckCircleIcon className="mx-auto h-12 w-12 text-green-400" />
                    <p className="mt-2 text-sm">All caught up! No pending tasks.</p>
                </div>
            )}

            {/* Tasks table */}
            {!loading && !error && tasks.length > 0 && (
                <div className="overflow-x-auto -mx-4 sm:mx-0">
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th className="text-xs">Workflow</th>
                                <th className="text-xs hidden sm:table-cell">Step</th>
                                <th className="text-xs">Summary</th>
                                <th className="text-xs hidden md:table-cell">Submitted</th>
                                <th className="text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {tasks.map((task) => (
                                <tr key={task.id}>
                                    <td>
                                        <div className="flex items-center gap-2">
                                            <WorkflowIcon className="h-4 w-4 text-gray-400 flex-shrink-0" />
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {task.workflow}
                                            </span>
                                        </div>
                                    </td>
                                    <td className="hidden sm:table-cell">
                                        <span className="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {task.step}
                                        </span>
                                    </td>
                                    <td className="text-gray-600 dark:text-gray-300">
                                        <div className="max-w-xs truncate" title={task.summary}>
                                            {task.summary}
                                        </div>
                                    </td>
                                    <td className="hidden md:table-cell text-gray-500 dark:text-gray-400 text-sm">
                                        {formatDateTime(task.submitted_at)}
                                    </td>
                                    <td>
                                        <a
                                            href={task.url}
                                            className="inline-flex items-center gap-1 text-sm link"
                                        >
                                            Open
                                            <ExternalLinkIcon className="h-3 w-3" />
                                        </a>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

// Helper functions
function formatDateTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}

// Icons
function CheckCircleIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}

function WorkflowIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
    );
}

function ExternalLinkIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
    );
}
