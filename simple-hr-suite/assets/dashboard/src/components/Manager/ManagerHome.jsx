import React from 'react';
import HeaderStrip from './HeaderStrip';
import QuickAccessCards from './QuickAccessCards';
import SalesOverview from './SalesOverview';
import KpiRow from './KpiRow';
import TeamSnapshot from './TeamSnapshot';
import MyHrPanel from './MyHrPanel';
import HrActions from './HrActions';
import FlowTasks from './FlowTasks';
import ReportsSection from './ReportsSection';

/**
 * Main manager dashboard view
 * Enhanced with Quick Access, Sales, Orders, and Reports sections
 */
export default function ManagerHome() {
    return (
        <div className="space-y-8">
            {/* Header with date range selector */}
            <HeaderStrip />

            {/* Quick Access Cards - tile navigation for main modules */}
            <QuickAccessCards />

            {/* Sales & Orders Overview */}
            <SalesOverview />

            {/* HR Section */}
            <div id="hr-section" className="space-y-6">
                {/* Section Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                            HR Overview
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Team attendance and HR management
                        </p>
                    </div>
                </div>

                {/* KPI cards row */}
                <KpiRow />

                {/* Main content grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Team Snapshot - takes 2 columns on desktop */}
                    <div className="lg:col-span-2" id="team-snapshot">
                        <TeamSnapshot />
                    </div>

                    {/* My HR Panel - takes 1 column on desktop */}
                    <div id="my-hr">
                        <MyHrPanel />
                    </div>
                </div>

                {/* HR Actions - full width */}
                <div id="hr-actions">
                    <HrActions />
                </div>
            </div>

            {/* Reports & Analytics */}
            <ReportsSection />

            {/* Workflows - full width */}
            <div id="workflows">
                <FlowTasks />
            </div>
        </div>
    );
}
