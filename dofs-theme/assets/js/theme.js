/**
 * DOFS Theme JavaScript
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initSidebar();
        initThemeToggle();
        initUserDropdown();
        initSearch();
    });

    /**
     * Sidebar Toggle (Mobile)
     */
    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close');

        if (!sidebar) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            if (backdrop) {
                backdrop.classList.remove('hidden');
            }
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
            document.body.classList.remove('overflow-hidden');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', openSidebar);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                closeSidebar();
            }
        });

        // Close on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    }

    /**
     * Theme Toggle (Dark/Light)
     */
    function initThemeToggle() {
        const toggleBtn = document.getElementById('theme-toggle');

        if (!toggleBtn) return;

        // Check for saved theme preference or default to system preference
        function getThemePreference() {
            const saved = localStorage.getItem('dofs-theme');
            if (saved) return saved;

            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            localStorage.setItem('dofs-theme', theme);
        }

        // Set initial theme
        setTheme(getThemePreference());

        // Toggle handler
        toggleBtn.addEventListener('click', function() {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('dofs-theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    /**
     * User Dropdown
     */
    function initUserDropdown() {
        const container = document.getElementById('user-dropdown-container');
        const toggleBtn = document.getElementById('user-dropdown-toggle');
        const menu = document.getElementById('user-dropdown-menu');

        if (!container || !toggleBtn || !menu) return;

        function openDropdown() {
            menu.classList.remove('hidden');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
            menu.classList.add('hidden');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }

        function toggleDropdown() {
            if (menu.classList.contains('hidden')) {
                openDropdown();
            } else {
                closeDropdown();
            }
        }

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                closeDropdown();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    }

    /**
     * Search (Cmd/Ctrl + K)
     */
    function initSearch() {
        const searchInput = document.getElementById('topbar-search');

        if (!searchInput) return;

        document.addEventListener('keydown', function(e) {
            // Cmd/Ctrl + K
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    }

})();
