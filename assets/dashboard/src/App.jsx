import React from 'react';
import { DashboardProvider } from './context/DashboardContext';
import { ThemeProvider } from './context/ThemeContext';
import AppShell from './components/Layout/AppShell';
import ManagerHome from './components/Manager/ManagerHome';

/**
 * Main App component
 */
export default function App() {
    return (
        <ThemeProvider>
            <DashboardProvider>
                <AppShell>
                    <ManagerHome />
                </AppShell>
            </DashboardProvider>
        </ThemeProvider>
    );
}
