import React, { useState, useRef, useEffect } from 'react';
import { useTheme } from '../../context/ThemeContext';
import { getBootData } from '../../api/client';

/**
 * Top navigation bar component
 */
export default function Topbar({ onMenuClick }) {
    const { isDark, toggleTheme } = useTheme();
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const userMenuRef = useRef(null);
    const boot = getBootData();

    // Close user menu when clicking outside
    useEffect(() => {
        function handleClickOutside(event) {
            if (userMenuRef.current && !userMenuRef.current.contains(event.target)) {
                setUserMenuOpen(false);
            }
        }

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <header className="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div className="flex items-center justify-between h-16 px-4 lg:px-6">
                {/* Left section: Menu button (mobile) + Logo + Nav items */}
                <div className="flex items-center gap-4">
                    {/* Mobile menu button */}
                    <button
                        type="button"
                        className="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700"
                        onClick={onMenuClick}
                        aria-label="Open menu"
                    >
                        <MenuIcon className="h-6 w-6" />
                    </button>

                    {/* Logo / System name */}
                    <div className="flex items-center gap-2">
                        <span className="text-xl font-semibold text-gray-900 dark:text-white">
                            Dar Dashboard
                        </span>
                    </div>

                    {/* Desktop navigation menu */}
                    <nav className="hidden lg:flex items-center gap-1 ms-6">
                        {boot.menu_items.map((item) => (
                            <a
                                key={item.id}
                                href={item.url}
                                target={item.target || '_self'}
                                className="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                            >
                                {item.title}
                            </a>
                        ))}
                    </nav>
                </div>

                {/* Center section: Search (UI placeholder) */}
                <div className="hidden md:flex flex-1 max-w-md mx-4">
                    <div className="relative w-full">
                        <SearchIcon className="absolute start-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                        <input
                            type="search"
                            placeholder="Search..."
                            className="w-full ps-10 pe-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 border-0 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500"
                            disabled
                            title="Search coming soon"
                        />
                    </div>
                </div>

                {/* Right section: Theme toggle + Notifications + User menu */}
                <div className="flex items-center gap-2">
                    {/* Theme toggle */}
                    <button
                        type="button"
                        onClick={toggleTheme}
                        className="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700"
                        aria-label={isDark ? 'Switch to light mode' : 'Switch to dark mode'}
                    >
                        {isDark ? (
                            <SunIcon className="h-5 w-5" />
                        ) : (
                            <MoonIcon className="h-5 w-5" />
                        )}
                    </button>

                    {/* Notifications (UI placeholder) */}
                    <button
                        type="button"
                        className="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 relative"
                        aria-label="Notifications"
                        disabled
                        title="Notifications coming soon"
                    >
                        <BellIcon className="h-5 w-5" />
                        <span className="absolute top-1.5 end-1.5 h-2 w-2 bg-red-500 rounded-full" />
                    </button>

                    {/* User menu */}
                    <div className="relative" ref={userMenuRef}>
                        <button
                            type="button"
                            onClick={() => setUserMenuOpen(!userMenuOpen)}
                            className="flex items-center gap-2 p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                            aria-expanded={userMenuOpen}
                            aria-haspopup="true"
                        >
                            {boot.user.avatar ? (
                                <img
                                    src={boot.user.avatar}
                                    alt=""
                                    className="h-8 w-8 rounded-full object-cover"
                                />
                            ) : (
                                <div className="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
                                    {boot.user.name?.charAt(0) || 'U'}
                                </div>
                            )}
                            <span className="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {boot.user.name}
                            </span>
                            <ChevronDownIcon className="hidden lg:block h-4 w-4 text-gray-500" />
                        </button>

                        {/* Dropdown menu */}
                        {userMenuOpen && (
                            <div className="absolute end-0 mt-2 w-48 py-1 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div className="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">
                                        {boot.user.name}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        {boot.user.roles?.[0] || 'User'}
                                    </p>
                                </div>
                                <a
                                    href="/profile"
                                    className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    My Profile
                                </a>
                                <a
                                    href="/my-hr"
                                    className="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    My HR Info
                                </a>
                                <hr className="my-1 border-gray-100 dark:border-gray-700" />
                                <a
                                    href="/wp-login.php?action=logout"
                                    className="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Logout
                                </a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}

// Icon components
function MenuIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    );
}

function SearchIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    );
}

function SunIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );
}

function MoonIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    );
}

function BellIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
    );
}

function ChevronDownIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    );
}
