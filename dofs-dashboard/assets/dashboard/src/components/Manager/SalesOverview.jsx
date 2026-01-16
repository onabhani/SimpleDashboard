import React from 'react';
import { useSalesData } from '../../hooks/useSalesData';
import MiniChart from '../Charts/MiniChart';

/**
 * Sales Overview Section with revenue stats and charts
 */
export default function SalesOverview() {
    const { data, loading, error } = useSalesData();

    const stats = data?.stats || {
        revenue: { value: 125000, change: 12.5, trend: 'up' },
        orders: { value: 1248, change: 8.2, trend: 'up' },
        avgOrder: { value: 485, change: -2.1, trend: 'down' },
        conversion: { value: 3.2, change: 0.5, trend: 'up' },
    };

    const recentOrders = data?.recentOrders || [];

    return (
        <div id="sales-section" className="space-y-6">
            {/* Section Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                        Sales Overview
                    </h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        Revenue and order performance
                    </p>
                </div>
                <div className="flex items-center gap-2">
                    <select className="text-sm border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Quarter</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard
                    title="Total Revenue"
                    value={formatCurrency(stats.revenue.value)}
                    change={stats.revenue.change}
                    trend={stats.revenue.trend}
                    icon={CurrencyIcon}
                    chartData={[30, 40, 35, 50, 49, 60, 70, 91, 85]}
                    chartColor="#3b82f6"
                    loading={loading}
                />
                <StatCard
                    title="Total Orders"
                    value={formatNumber(stats.orders.value)}
                    change={stats.orders.change}
                    trend={stats.orders.trend}
                    icon={ShoppingCartIcon}
                    chartData={[65, 59, 80, 81, 56, 55, 70, 65, 80]}
                    chartColor="#10b981"
                    loading={loading}
                />
                <StatCard
                    title="Avg. Order Value"
                    value={formatCurrency(stats.avgOrder.value)}
                    change={stats.avgOrder.change}
                    trend={stats.avgOrder.trend}
                    icon={ReceiptIcon}
                    chartData={[45, 52, 38, 45, 42, 50, 46, 48, 42]}
                    chartColor="#8b5cf6"
                    loading={loading}
                />
                <StatCard
                    title="Conversion Rate"
                    value={`${stats.conversion.value}%`}
                    change={stats.conversion.change}
                    trend={stats.conversion.trend}
                    icon={TrendingUpIcon}
                    chartData={[28, 30, 32, 35, 33, 38, 35, 40, 42]}
                    chartColor="#f59e0b"
                    loading={loading}
                />
            </div>

            {/* Orders Section */}
            <div id="orders-section" className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Recent Orders */}
                <div className="lg:col-span-2 dashboard-card">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                            Recent Orders
                        </h3>
                        <a href="/orders" className="text-sm link">
                            View All
                        </a>
                    </div>

                    {loading ? (
                        <LoadingSkeleton rows={5} />
                    ) : (
                        <div className="overflow-x-auto -mx-4 sm:mx-0">
                            <table className="data-table">
                                <thead>
                                    <tr>
                                        <th className="text-xs">Order ID</th>
                                        <th className="text-xs">Customer</th>
                                        <th className="text-xs hidden sm:table-cell">Items</th>
                                        <th className="text-xs">Amount</th>
                                        <th className="text-xs">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {(recentOrders.length > 0 ? recentOrders : mockOrders).map((order) => (
                                        <tr key={order.id}>
                                            <td className="font-medium text-primary-600 dark:text-primary-400">
                                                #{order.id}
                                            </td>
                                            <td className="text-gray-900 dark:text-white">
                                                {order.customer}
                                            </td>
                                            <td className="hidden sm:table-cell text-gray-600 dark:text-gray-400">
                                                {order.items} items
                                            </td>
                                            <td className="font-medium text-gray-900 dark:text-white">
                                                {formatCurrency(order.amount)}
                                            </td>
                                            <td>
                                                <OrderStatusBadge status={order.status} />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* Order Status Summary */}
                <div className="dashboard-card">
                    <h3 className="font-semibold text-gray-900 dark:text-white mb-4">
                        Order Status
                    </h3>

                    <div className="space-y-4">
                        <StatusItem
                            label="Pending"
                            count={24}
                            total={100}
                            color="bg-yellow-500"
                        />
                        <StatusItem
                            label="Processing"
                            count={35}
                            total={100}
                            color="bg-blue-500"
                        />
                        <StatusItem
                            label="Shipped"
                            count={28}
                            total={100}
                            color="bg-purple-500"
                        />
                        <StatusItem
                            label="Delivered"
                            count={85}
                            total={100}
                            color="bg-green-500"
                        />
                        <StatusItem
                            label="Cancelled"
                            count={8}
                            total={100}
                            color="bg-red-500"
                        />
                    </div>

                    <div className="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div className="flex items-center justify-between text-sm">
                            <span className="text-gray-500 dark:text-gray-400">Total Orders</span>
                            <span className="font-semibold text-gray-900 dark:text-white">180</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

/**
 * Stat Card Component
 */
function StatCard({ title, value, change, trend, icon: Icon, chartData, chartColor, loading }) {
    if (loading) {
        return (
            <div className="dashboard-card">
                <div className="skeleton h-4 w-24 mb-2" />
                <div className="skeleton h-8 w-32 mb-2" />
                <div className="skeleton h-3 w-20" />
            </div>
        );
    }

    return (
        <div className="dashboard-card">
            <div className="flex items-start justify-between mb-3">
                <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">
                        {title}
                    </p>
                    <p className="text-2xl font-bold text-gray-900 dark:text-white">
                        {value}
                    </p>
                </div>
                <div className="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <Icon className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </div>
            </div>

            <div className="flex items-center justify-between">
                <div className="flex items-center gap-1">
                    {trend === 'up' ? (
                        <ArrowUpIcon className="h-4 w-4 text-green-500" />
                    ) : (
                        <ArrowDownIcon className="h-4 w-4 text-red-500" />
                    )}
                    <span className={`text-sm font-medium ${trend === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`}>
                        {Math.abs(change)}%
                    </span>
                    <span className="text-xs text-gray-500 dark:text-gray-400">
                        vs last month
                    </span>
                </div>
            </div>

            {/* Mini sparkline chart */}
            <div className="mt-3 h-10">
                <MiniChart data={chartData} color={chartColor} />
            </div>
        </div>
    );
}

/**
 * Status Item for order summary
 */
function StatusItem({ label, count, total, color }) {
    const percentage = (count / total) * 100;

    return (
        <div>
            <div className="flex items-center justify-between mb-1">
                <span className="text-sm text-gray-600 dark:text-gray-400">{label}</span>
                <span className="text-sm font-medium text-gray-900 dark:text-white">{count}</span>
            </div>
            <div className="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div
                    className={`h-full ${color} rounded-full transition-all duration-500`}
                    style={{ width: `${percentage}%` }}
                />
            </div>
        </div>
    );
}

/**
 * Order Status Badge
 */
function OrderStatusBadge({ status }) {
    const styles = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    };

    return (
        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize ${styles[status] || styles.pending}`}>
            {status}
        </span>
    );
}

function LoadingSkeleton({ rows }) {
    return (
        <div className="space-y-3">
            {Array.from({ length: rows }).map((_, i) => (
                <div key={i} className="flex items-center gap-4">
                    <div className="skeleton h-4 w-16" />
                    <div className="skeleton h-4 w-32" />
                    <div className="skeleton h-4 w-12 hidden sm:block" />
                    <div className="skeleton h-4 w-20" />
                    <div className="skeleton h-5 w-16 rounded-full" />
                </div>
            ))}
        </div>
    );
}

// Mock data
const mockOrders = [
    { id: '10234', customer: 'Ahmed Al-Rashid', items: 3, amount: 1250, status: 'delivered' },
    { id: '10233', customer: 'Sara Mohammed', items: 1, amount: 450, status: 'shipped' },
    { id: '10232', customer: 'Khalid Ibrahim', items: 5, amount: 2100, status: 'processing' },
    { id: '10231', customer: 'Fatima Hassan', items: 2, amount: 890, status: 'pending' },
    { id: '10230', customer: 'Omar Nasser', items: 4, amount: 1680, status: 'delivered' },
];

// Helper functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0,
    }).format(amount);
}

function formatNumber(num) {
    return new Intl.NumberFormat('en-SA').format(num);
}

// Icons
function CurrencyIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}

function ShoppingCartIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    );
}

function ReceiptIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
        </svg>
    );
}

function TrendingUpIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
    );
}

function ArrowUpIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    );
}

function ArrowDownIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    );
}
