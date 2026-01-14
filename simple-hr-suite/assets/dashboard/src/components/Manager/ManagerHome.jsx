import React from 'react';
import HeaderStrip from './HeaderStrip';
import KpiRow from './KpiRow';
import TeamSnapshot from './TeamSnapshot';
import MyHrPanel from './MyHrPanel';
import HrActions from './HrActions';
import FlowTasks from './FlowTasks';

/**
 * Main manager dashboard view
 */
export default function ManagerHome() {
    return (
        <div className="space-y-6">
            {/* Header with date range selector */}
            <HeaderStrip />

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

            {/* Workflows - full width */}
            <div id="workflows">
                <FlowTasks />
            </div>
        </div>
    );
}
