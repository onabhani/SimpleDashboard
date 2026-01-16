import React from 'react';

/**
 * KPI card component
 */
export default function KpiCard({
    title,
    mainStat,
    subtext,
    icon: Icon,
    variant = 'default',
    onClick,
    actions,
}) {
    const variantStyles = {
        default: 'text-gray-500 dark:text-gray-400',
        success: 'text-green-500 dark:text-green-400',
        warning: 'text-orange-500 dark:text-orange-400',
        danger: 'text-red-500 dark:text-red-400',
    };

    const variantBgStyles = {
        default: 'bg-gray-100 dark:bg-gray-700',
        success: 'bg-green-100 dark:bg-green-900/30',
        warning: 'bg-orange-100 dark:bg-orange-900/30',
        danger: 'bg-red-100 dark:bg-red-900/30',
    };

    const CardWrapper = onClick ? 'button' : 'div';
    const cardProps = onClick
        ? {
              type: 'button',
              onClick,
              className: 'kpi-card w-full text-start',
          }
        : {
              className: 'dashboard-card',
          };

    return (
        <CardWrapper {...cardProps}>
            <div className="flex items-start justify-between">
                <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {title}
                    </p>
                    <p className="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {mainStat}
                    </p>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {subtext}
                    </p>
                </div>
                {Icon && (
                    <div className={`flex-shrink-0 p-3 rounded-lg ${variantBgStyles[variant]}`}>
                        <Icon className={`h-6 w-6 ${variantStyles[variant]}`} />
                    </div>
                )}
            </div>

            {/* Action buttons */}
            {actions && actions.length > 0 && (
                <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
                    {actions.map((action, index) => (
                        <a
                            key={index}
                            href={action.href}
                            onClick={(e) => e.stopPropagation()}
                            className="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline"
                        >
                            {action.label}
                        </a>
                    ))}
                </div>
            )}
        </CardWrapper>
    );
}
