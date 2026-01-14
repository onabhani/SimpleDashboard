import React from 'react';
import BarChart from '../Charts/BarChart';
import DoughnutChart from '../Charts/DoughnutChart';

/**
 * Reports & Analytics Section
 */
export default function ReportsSection() {
    // Mock data for charts
    const monthlyRevenue = [85000, 92000, 78000, 105000, 115000, 98000, 125000];
    const monthLabels = ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'];

    const categoryData = [35, 25, 20, 12, 8];
    const categoryLabels = ['Electronics', 'Fashion', 'Home', 'Sports', 'Other'];
    const categoryColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#6b7280'];

    const topProducts = [
        { name: 'iPhone 15 Pro Max', sales: 245, revenue: 489500, trend: 'up' },
        { name: 'Samsung Galaxy S24', sales: 189, revenue: 283500, trend: 'up' },
        { name: 'MacBook Pro 14"', sales: 92, revenue: 276000, trend: 'down' },
        { name: 'iPad Pro 12.9"', sales: 156, revenue: 187200, trend: 'up' },
        { name: 'AirPods Pro 2', sales: 312, revenue: 93600, trend: 'up' },
    ];

    const recentReports = [
        { id: 1, name: 'Monthly Sales Report', type: 'Sales', date: '2026-01-14', status: 'ready' },
        { id: 2, name: 'Q4 Performance Analysis', type: 'Analytics', date: '2026-01-12', status: 'ready' },
        { id: 3, name: 'Inventory Turnover Report', type: 'Inventory', date: '2026-01-10', status: 'ready' },
        { id: 4, name: 'Customer Acquisition Report', type: 'Marketing', date: '2026-01-08', status: 'processing' },
    ];

    return (
        <div id="reports-section" className="space-y-6">
            {/* Section Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                        Reports & Analytics
                    </h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        Data insights and performance metrics
                    </p>
                </div>
                <button className="btn-primary text-sm">
                    <PlusIcon className="h-4 w-4 me-1.5" />
                    Generate Report
                </button>
            </div>

            {/* Charts Row */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Revenue Trend */}
                <div className="lg:col-span-2 dashboard-card">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                            Revenue Trend
                        </h3>
                        <select className="text-xs border border-gray-200 dark:border-gray-700 rounded-md px-2 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                            <option>Last 7 months</option>
                            <option>Last 12 months</option>
                        </select>
                    </div>
                    <BarChart
                        data={monthlyRevenue}
                        labels={monthLabels}
                        color="#3b82f6"
                        height={220}
                    />
                </div>

                {/* Sales by Category */}
                <div className="dashboard-card">
                    <h3 className="font-semibold text-gray-900 dark:text-white mb-4">
                        Sales by Category
                    </h3>
                    <DoughnutChart
                        data={categoryData}
                        labels={categoryLabels}
                        colors={categoryColors}
                        centerText={{ value: '100%', label: 'Total' }}
                    />
                </div>
            </div>

            {/* Bottom Row */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Top Products */}
                <div className="dashboard-card">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                            Top Products
                        </h3>
                        <a href="/products" className="text-sm link">View All</a>
                    </div>
                    <div className="space-y-3">
                        {topProducts.map((product, index) => (
                            <div key={index} className="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div className="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {index + 1}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {product.name}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        {product.sales} sold
                                    </p>
                                </div>
                                <div className="text-end">
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">
                                        {formatCurrency(product.revenue)}
                                    </p>
                                    <div className="flex items-center justify-end gap-0.5">
                                        {product.trend === 'up' ? (
                                            <TrendUpIcon className="h-3 w-3 text-green-500" />
                                        ) : (
                                            <TrendDownIcon className="h-3 w-3 text-red-500" />
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Recent Reports */}
                <div className="dashboard-card">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                            Recent Reports
                        </h3>
                        <a href="/reports" className="text-sm link">View All</a>
                    </div>
                    <div className="space-y-3">
                        {recentReports.map((report) => (
                            <div key={report.id} className="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                                <div className="flex-shrink-0">
                                    <ReportIcon className="h-8 w-8 text-gray-400" />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {report.name}
                                    </p>
                                    <div className="flex items-center gap-2 mt-0.5">
                                        <span className="text-xs text-gray-500 dark:text-gray-400">
                                            {report.type}
                                        </span>
                                        <span className="text-gray-300 dark:text-gray-600">|</span>
                                        <span className="text-xs text-gray-500 dark:text-gray-400">
                                            {formatDate(report.date)}
                                        </span>
                                    </div>
                                </div>
                                <div className="flex items-center gap-2">
                                    {report.status === 'ready' ? (
                                        <button className="p-1.5 rounded-md bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                                            <DownloadIcon className="h-4 w-4" />
                                        </button>
                                    ) : (
                                        <span className="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Processing
                                        </span>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Quick Stats Grid */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <QuickStat
                    label="Total Customers"
                    value="5,248"
                    change="+12%"
                    trend="up"
                    icon={UsersIcon}
                />
                <QuickStat
                    label="Active Products"
                    value="1,234"
                    change="+5%"
                    trend="up"
                    icon={CubeIcon}
                />
                <QuickStat
                    label="Pending Shipments"
                    value="89"
                    change="-8%"
                    trend="down"
                    icon={TruckIcon}
                />
                <QuickStat
                    label="Returns Rate"
                    value="2.4%"
                    change="-0.3%"
                    trend="down"
                    icon={ReturnIcon}
                />
            </div>
        </div>
    );
}

/**
 * Quick stat card
 */
function QuickStat({ label, value, change, trend, icon: Icon }) {
    return (
        <div className="dashboard-card">
            <div className="flex items-center justify-between mb-2">
                <Icon className="h-5 w-5 text-gray-400" />
                <span className={`text-xs font-medium ${trend === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`}>
                    {change}
                </span>
            </div>
            <p className="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                {value}
            </p>
            <p className="text-xs text-gray-500 dark:text-gray-400">
                {label}
            </p>
        </div>
    );
}

// Helper functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0,
    }).format(amount);
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
    });
}

// Icons
function PlusIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
        </svg>
    );
}

function TrendUpIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    );
}

function TrendDownIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    );
}

function ReportIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    );
}

function DownloadIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
    );
}

function UsersIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );
}

function CubeIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    );
}

function TruckIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M8 17h1m10-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-1m-1 0h-5m-5 0H5a2 2 0 01-2-2V9a2 2 0 012-2h1m7 0V5a2 2 0 00-2-2H8a2 2 0 00-2 2v2m8 0H6m12 0a2 2 0 012 2v3" />
        </svg>
    );
}

function ReturnIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
        </svg>
    );
}
