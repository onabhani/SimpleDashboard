import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';

/**
 * Theme context for managing dark mode
 */
const ThemeContext = createContext(null);

/**
 * Theme provider component
 */
export function ThemeProvider({ children }) {
    // Initialize theme from localStorage or system preference
    const [isDark, setIsDark] = useState(() => {
        // Check localStorage first
        const stored = localStorage.getItem('sfs-dashboard-theme');
        if (stored) {
            return stored === 'dark';
        }

        // Fall back to system preference
        if (typeof window !== 'undefined' && window.matchMedia) {
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        return false;
    });

    // Apply theme class to dashboard root
    useEffect(() => {
        const root = document.getElementById('sfs-hr-dashboard-root');
        if (root) {
            if (isDark) {
                root.classList.add('sfs-dashboard--dark');
            } else {
                root.classList.remove('sfs-dashboard--dark');
            }
        }

        // Store preference
        localStorage.setItem('sfs-dashboard-theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    // Toggle theme
    const toggleTheme = useCallback(() => {
        setIsDark((prev) => !prev);
    }, []);

    // Set specific theme
    const setTheme = useCallback((dark) => {
        setIsDark(dark);
    }, []);

    const value = {
        isDark,
        toggleTheme,
        setTheme,
    };

    return (
        <ThemeContext.Provider value={value}>
            {children}
        </ThemeContext.Provider>
    );
}

/**
 * Hook to access theme context
 */
export function useTheme() {
    const context = useContext(ThemeContext);

    if (!context) {
        throw new Error('useTheme must be used within a ThemeProvider');
    }

    return context;
}

export default ThemeContext;
