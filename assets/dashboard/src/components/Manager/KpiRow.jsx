import React from 'react';
import KpiCard from './KpiCard';
import { useSummary } from '../../hooks/useSummary';

/**
 * KPI cards row component
 */
export default function KpiRow() {
    const { data, loading, error } = useSummary();

    if (loading) {
        return (
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {[1, 2, 3].map((i) => (
                    <div key={i} className="dashboard-card">
                        <div className="space-y-3">
                            <div className="skeleton h-4 w-24" />
                            <div className="skeleton h-8 w-16" />
                            <div className="skeleton h-3 w-32" />
                        </div>
                    </div>
                ))}
            </div>
        );
    }

    if (error) {
        return (
            <div className="dashboard-card text-center text-red-600 dark:text-red-400 py-8">
                Failed to load summary data. Please refresh the page.
            </div>
        );
    }

    const { team_attendance, hr_pending, my_hr } = data || {};

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {/* Team Attendance */}
            <KpiCard
                title="Team Attendance"
                mainStat={`${team_attendance?.present || 0} / ${team_attendance?.total || 0}`}
                subtext={`${team_attendance?.absent || 0} absent · ${team_attendance?.on_leave || 0} on leave · ${team_attendance?.late || 0} late`}
                icon={UsersIcon}
                onClick={() => {
                    document.getElementById('team-snapshot')?.scrollIntoView({ behavior: 'smooth' });
                }}
            />

            {/* HR Pending */}
            <KpiCard
                title="HR Pending"
                mainStat={String(hr_pending?.total || 0)}
                subtext={`${hr_pending?.leave || 0} leave requests · ${hr_pending?.loan || 0} loan requests`}
                icon={ClipboardIcon}
                variant={hr_pending?.total > 0 ? 'warning' : 'default'}
                onClick={() => {
                    document.getElementById('hr-actions')?.scrollIntoView({ behavior: 'smooth' });
                }}
            />

            {/* My HR Status */}
            <KpiCard
                title="My HR Status"
                mainStat={my_hr?.status_label || 'Unknown'}
                subtext={my_hr?.first_punch ? `Clocked-in ${my_hr.first_punch}` : 'No punch yet'}
                icon={UserIcon}
                variant={my_hr?.status === 'present' ? 'success' : 'default'}
                actions={[
                    { label: 'Punch Now', href: '/my-attendance' },
                    { label: 'Request Leave', href: '/leave-request' },
                    { label: 'Request Loan', href: '/loan-request' },
                ]}
            />
        </div>
    );
}

// Icon components
function UsersIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );
}

function ClipboardIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
    );
}

function UserIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
    );
}
