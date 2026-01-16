import React, { useState } from 'react';
import Topbar from './Topbar';
import Sidebar from './Sidebar';

/**
 * Main application shell with topbar and sidebar layout
 */
export default function AppShell({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <div className="flex flex-col min-h-screen">
            {/* Topbar */}
            <Topbar onMenuClick={() => setSidebarOpen(!sidebarOpen)} />

            {/* Main content area */}
            <div className="flex flex-1 overflow-hidden">
                {/* Sidebar */}
                <Sidebar isOpen={sidebarOpen} onClose={() => setSidebarOpen(false)} />

                {/* Main content */}
                <main className="flex-1 overflow-y-auto p-4 lg:p-6 bg-background-light dark:bg-background-dark">
                    <div className="max-w-7xl mx-auto">
                        {children}
                    </div>
                </main>
            </div>

            {/* Mobile sidebar overlay */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 bg-black/50 z-20 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                    aria-hidden="true"
                />
            )}
        </div>
    );
}
