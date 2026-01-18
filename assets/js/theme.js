/**
 * DOFS Theme JavaScript
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initSidebar();
        initThemeToggle();
        initNotifications();
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
     * Notifications Dropdown
     */
    function initNotifications() {
        const container = document.getElementById('notifications-container');
        const toggleBtn = document.getElementById('notifications-toggle');
        const dropdown = document.getElementById('notifications-dropdown');

        if (!container || !toggleBtn || !dropdown) return;

        function openDropdown() {
            dropdown.classList.remove('hidden');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }

        function toggleDropdown() {
            if (dropdown.classList.contains('hidden')) {
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
     * Search with Gravity Forms entries
     */
    function initSearch() {
        const searchInput = document.getElementById('topbar-search');
        const searchContainer = document.getElementById('search-container');
        const searchResults = document.getElementById('search-results');
        const searchResultsList = document.getElementById('search-results-list');
        const searchLoading = document.getElementById('search-loading');

        if (!searchInput || !searchResults || !searchResultsList) return;

        let searchTimeout = null;
        let currentQuery = '';

        // Handle input
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            currentQuery = query;

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Hide results if query is too short
            if (query.length < 2) {
                hideResults();
                return;
            }

            // Debounce search
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 300);
        });

        // Handle focus
        searchInput.addEventListener('focus', function() {
            if (currentQuery.length >= 2 && searchResultsList.children.length > 0) {
                showResults();
            }
        });

        // Close results on click outside
        document.addEventListener('click', function(e) {
            if (searchContainer && !searchContainer.contains(e.target)) {
                hideResults();
            }
        });

        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideResults();
                searchInput.blur();
            }
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            const items = searchResultsList.querySelectorAll('a');
            if (!items.length) return;

            const activeItem = searchResultsList.querySelector('a.bg-gray-100, a.dark\\:bg-gray-700');
            let index = Array.from(items).indexOf(activeItem);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                index = index < items.length - 1 ? index + 1 : 0;
                highlightItem(items, index);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                index = index > 0 ? index - 1 : items.length - 1;
                highlightItem(items, index);
            } else if (e.key === 'Enter' && activeItem) {
                e.preventDefault();
                activeItem.click();
            }
        });

        function highlightItem(items, index) {
            items.forEach(function(item, i) {
                if (i === index) {
                    item.classList.add('bg-gray-100', 'dark:bg-gray-700');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                }
            });
        }

        function showResults() {
            searchResults.classList.remove('hidden');
        }

        function hideResults() {
            searchResults.classList.add('hidden');
        }

        function showLoading() {
            if (searchLoading) searchLoading.classList.remove('hidden');
        }

        function hideLoading() {
            if (searchLoading) searchLoading.classList.add('hidden');
        }

        function performSearch(query) {
            showLoading();

            // Build the API URL
            var baseUrl = window.location.origin;
            var restUrl = '/wp-json/sfs-hr/v1/dashboard/search/entries';

            if (window.dofsTheme && window.dofsTheme.restUrl) {
                restUrl = window.dofsTheme.restUrl + 'search/entries';
            }

            var url = baseUrl + restUrl + '?q=' + encodeURIComponent(query);

            var headers = {
                'Content-Type': 'application/json'
            };

            if (window.dofsTheme && window.dofsTheme.nonce) {
                headers['X-WP-Nonce'] = window.dofsTheme.nonce;
            }

            fetch(url, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok) {
                        throw new Error(data.message || 'Search failed');
                    }
                    return data;
                });
            })
            .then(function(data) {
                hideLoading();

                if (query !== currentQuery) return; // Query changed while fetching

                if (data.results && data.results.length > 0) {
                    renderResults(data.results, data.total);
                    showResults();
                } else if (data.code) {
                    // API returned an error
                    renderErrorMessage(data.message || 'Search error');
                    showResults();
                } else {
                    renderNoResults(query);
                    showResults();
                }
            })
            .catch(function(error) {
                hideLoading();
                console.error('Search error:', error);
                renderErrorMessage(error.message || 'Search failed. Please try again.');
                showResults();
            });
        }

        function renderResults(results, total) {
            let html = '';

            if (total > results.length) {
                html += '<div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">';
                html += 'Showing ' + results.length + ' of ' + total + ' results';
                html += '</div>';
            }

            results.forEach(function(result) {
                html += '<a href="' + escapeHtml(result.edit_url) + '" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0">';
                html += '<div class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">';
                html += '<span class="text-primary-600 dark:text-primary-400 text-xs font-bold">#' + result.entry_id + '</span>';
                html += '</div>';
                html += '<div class="flex-1 min-w-0">';
                html += '<p class="text-sm font-medium text-gray-900 dark:text-white truncate">' + escapeHtml(result.primary_value) + '</p>';
                html += '<p class="text-xs text-gray-500 dark:text-gray-400">' + escapeHtml(result.form_title) + '</p>';
                html += '<p class="text-xs text-gray-400 dark:text-gray-500">' + formatDate(result.date_created) + '</p>';
                html += '</div>';
                html += '</a>';
            });

            searchResultsList.innerHTML = html;
        }

        function renderNoResults(query) {
            searchResultsList.innerHTML = '<div class="px-4 py-8 text-center">' +
                '<p class="text-sm text-gray-500 dark:text-gray-400">No entries found for "' + escapeHtml(query) + '"</p>' +
                '</div>';
        }

        function renderErrorMessage(message) {
            searchResultsList.innerHTML = '<div class="px-4 py-8 text-center">' +
                '<p class="text-sm text-red-500 dark:text-red-400">' + escapeHtml(message) + '</p>' +
                '</div>';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

})();
