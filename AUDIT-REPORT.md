# Code Audit Report — SimpleDashboard Plugin

**Date:** 2026-03-19
**Scope:** Full codebase audit covering all PHP, JS, and JSX files
**Files Audited:** 73 files across the theme and dashboard module
**Issues Found:** 21
**Issues Fixed:** 16
**Remaining (Advisory):** 5

---

## Table of Contents

1. [Security Issues](#1-security-issues)
2. [Performance Issues](#2-performance-issues)
3. [Duplication Issues](#3-duplication-issues)
4. [Logic Issues](#4-logic-issues)
5. [Summary Table](#5-summary-table)

---

## 1. Security Issues

### S1. XSS in Timezone Dropdown — `page-settings.php:301`

**Severity:** Medium
**Status:** Fixed

**Problem:**
Timezone values were output without escaping inside a raw HTML string:

```php
// BEFORE (vulnerable)
foreach ($timezones as $tz) {
    $selected = $user_settings['timezone'] === $tz ? 'selected' : '';
    echo "<option value=\"{$tz}\" {$selected}>{$tz}</option>";
}
```

While `timezone_identifiers_list()` returns safe values today, this violates defense-in-depth and could become exploitable if the data source changes.

**Fix:**
Switched to proper WordPress escaping functions:

```php
// AFTER (safe)
foreach ($timezones as $tz) :
?>
<option value="<?php echo esc_attr($tz); ?>" <?php selected($user_settings['timezone'], $tz); ?>>
    <?php echo esc_html($tz); ?>
</option>
<?php endforeach;
```

---

### S2. Unsafe `wp_redirect()`/`exit` Inside Shortcode — `Shortcode.php:33-35`

**Severity:** High
**Status:** Fixed

**Problem:**
Calling `wp_redirect()` + `exit` inside a shortcode's `render()` method is unsafe. Shortcodes execute after HTTP headers are sent, so:
- `wp_redirect()` triggers a PHP "headers already sent" warning
- `exit` abruptly halts all page output mid-render

```php
// BEFORE (unsafe)
public function render($atts = []): string {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit;
    }
```

**Fix:**
Return an HTML login link instead of attempting a redirect:

```php
// AFTER (safe)
if (!is_user_logged_in()) {
    return '<div class="dofs-dashboard-login-required" style="padding: 40px; text-align: center;">
        <h2>' . esc_html__('Login Required', 'dofs-theme') . '</h2>
        <p><a href="' . esc_url(wp_login_url(get_permalink())) . '">'
            . esc_html__('Please log in to access the dashboard.', 'dofs-theme') . '</a></p>
    </div>';
}
```

---

### S3. Missing Auth Check in GravityFormsSearch — `GravityFormsSearch.php:58`

**Severity:** High
**Status:** Fixed

**Problem:**
Unlike all other endpoints, `GravityFormsSearch::check_permission()` did not check `is_user_logged_in()` and returned a plain `bool` instead of `WP_Error`. Unauthenticated users received a generic 403 instead of a proper 401, and the `current_user_can('administrator')` check was redundant (administrators already have all capabilities).

```php
// BEFORE (missing auth check)
public function check_permission(): bool {
    return current_user_can('gravityforms_view_entries') ||
           current_user_can('dofs.view_dashboard') ||
           current_user_can('administrator');
}
```

**Fix:**
Added explicit login check with `WP_Error` response, and replaced the redundant `administrator` check with the correct `sfs_hr.view_dashboard_manager` capability:

```php
// AFTER (proper auth)
public function check_permission() {
    if (!is_user_logged_in()) {
        return new \WP_Error('rest_not_logged_in', __('...'), ['status' => 401]);
    }
    if (!current_user_can('gravityforms_view_entries') &&
        !current_user_can('dofs.view_dashboard') &&
        !current_user_can('sfs_hr.view_dashboard_manager')) {
        return new \WP_Error('rest_forbidden', __('...'), ['status' => 403]);
    }
    return true;
}
```

---

### S4. Unsanitized `$_SERVER['REQUEST_URI']` — `functions.php:359,530`

**Severity:** Medium
**Status:** Fixed

**Problem:**
`$_SERVER['REQUEST_URI']` was used directly without sanitization in two locations:

```php
// BEFORE (unsanitized)
$current_url = trailingslashit($_SERVER['REQUEST_URI']);
```

**Fix:**
Applied `sanitize_url()` before use:

```php
// AFTER (sanitized)
$current_url = trailingslashit(sanitize_url($_SERVER['REQUEST_URI']));
```

---

### S5. User Email and Roles Exposed to Frontend JS — `functions.php:108`

**Severity:** Low
**Status:** Fixed

**Problem:**
`dofs_get_current_user_data()` was exposing `user_email` and `roles` in the `wp_localize_script` output, making them visible in the page source to any authenticated user or browser extension:

```php
// BEFORE (leaking sensitive data)
return [
    'id' => $user->ID,
    'name' => $user->display_name,
    'email' => $user->user_email,   // exposed
    'avatar' => get_avatar_url($user->ID, ['size' => 96]),
    'roles' => $user->roles,         // exposed
];
```

**Fix:**
Removed `email` and `roles` from the frontend data (neither was consumed by the theme JS):

```php
// AFTER (minimal data)
return [
    'id' => $user->ID,
    'name' => $user->display_name,
    'avatar' => get_avatar_url($user->ID, ['size' => 96]),
];
```

---

## 2. Performance Issues

### P1. GravityFormsSearch Loads All Entries Across All Forms — `GravityFormsSearch.php:186`

**Severity:** High
**Status:** Fixed

**Problem:**
The search endpoint loaded up to 200 entries per form with no total cap. With 50 forms, that's potentially 10,000 entries loaded into memory, sorted in PHP, then paginated — a classic memory/CPU bomb.

Additionally, when the search query was numeric, the entry could appear twice in results (fetched by ID AND by field search).

```php
// BEFORE (unbounded + duplicates possible)
$entries = \GFAPI::get_entries($form_id, $search_criteria, null,
    ['offset' => 0, 'page_size' => 200]);
```

**Fix:**
- Capped per-form results to `min(per_page * 3, 100)`
- Added a `$seen_ids` deduplication map to prevent duplicate entries

```php
// AFTER (bounded + deduplicated)
$max_results_per_form = min($per_page * 3, 100);
// ...
$entries = \GFAPI::get_entries($form_id, $search_criteria, null,
    ['offset' => 0, 'page_size' => $limit]);
// ...
$key = $result['form_id'] . '-' . $result['entry_id'];
if (!isset($seen_ids[$key])) {
    $seen_ids[$key] = true;
    $all_results[] = $result;
}
```

---

### P2. FlowTasks Iterates ALL Forms and ALL Entries — `FlowTasks.php:100-128`

**Severity:** High
**Status:** Fixed

**Problem:**
`get_real_gravityflow_tasks()` loaded ALL active entries from EVERY form with no pagination limit, then iterated each to check step assignees:

```php
// BEFORE (no limit)
$entries = \GFAPI::get_entries($form_id, $search_criteria);
```

**Fix:**
Added pagination limit of 50 entries per form:

```php
// AFTER (bounded)
$entries = \GFAPI::get_entries($form_id, $search_criteria, null,
    ['offset' => 0, 'page_size' => 50]);
```

---

### P3. Counting Pending Tasks Fetches 100 Full Task Objects — `functions.php:1193`

**Severity:** Medium
**Status:** Not Fixed (Advisory)

**Problem:**
`dofs_get_user_pending_tasks_count()` calls `dofs_get_user_workflow_tasks($user_id, 100)` to retrieve 100 full task objects, then filters with `array_filter` just to count them.

```php
function dofs_get_user_pending_tasks_count($user_id = null): int {
    $tasks = dofs_get_user_workflow_tasks($user_id, 100);
    return count(array_filter($tasks, function($task) {
        return $task['status'] !== 'complete' && $task['status'] !== 'completed';
    }));
}
```

**Recommendation:**
Create a dedicated count query that uses `GFAPI::count_entries()` instead of fetching full entry data.

---

### P4. Redundant `get_nav_menu_locations()` Calls — `Shortcode.php:161`

**Severity:** Low
**Status:** Not Fixed (Advisory)

**Problem:**
`get_topbar_menu()` calls `get_nav_menu_locations()`, then passes the location string to `get_menu_items()`, which calls `get_nav_menu_locations()` again.

**Recommendation:**
Pass the locations array to `get_menu_items()` instead of the location string, or cache the result. This is a minor issue since WordPress internally caches this data.

---

### P5. `useApi` Hook Infinite Re-render Risk — `useApi.js:30-32`

**Severity:** High
**Status:** Fixed

**Problem:**
The `fetchFn` parameter was used as a `useCallback` dependency, but callers typically pass inline arrow functions (e.g., `() => api.getSalesData({range})`), causing the callback to re-create on every render, which triggers the `useEffect` endlessly:

```js
// BEFORE (infinite loop risk)
const fetch = useCallback(async () => {
    // ...
    const result = await fetchFn();
    // ...
}, [fetchFn]);  // fetchFn changes every render!

useEffect(() => {
    fetch();
}, [...deps, fetch]);  // triggers every render
```

**Fix:**
Used `useRef` to hold the latest `fetchFn` without causing re-renders, and removed it from dependencies:

```js
// AFTER (stable)
const fetchFnRef = useRef(fetchFn);
fetchFnRef.current = fetchFn;

const doFetch = useCallback(async () => {
    const result = await fetchFnRef.current();
    // ...
}, []);  // stable - never re-creates

useEffect(() => {
    doFetch();
}, deps);  // only re-runs when deps change
```

---

## 3. Duplication Issues

### D1. Sidebar Menu Logic Fully Duplicated — `functions.php:132-186` vs `Shortcode.php:192-259`

**Severity:** Medium
**Status:** Not Fixed (Advisory)

**Problem:**
Nearly identical hierarchical menu-building code exists in both `dofs_get_sidebar_menu()` (functions.php) and `Shortcode::get_sidebar_menu()`. Both:
1. Call `get_nav_menu_locations()`
2. Fetch the menu object
3. Get menu items
4. Separate parents from children
5. Attach children to parents
6. Filter empty sections

**Recommendation:**
Have the Shortcode delegate to `dofs_get_sidebar_menu()` from functions.php, or extract the shared logic to a utility function.

---

### D2. Default Sidebar Menus Duplicated AND Inconsistent — `functions.php:208-257` vs `Shortcode.php:287-341`

**Severity:** Medium
**Status:** Not Fixed (Advisory)

**Problem:**
Two completely different default menus exist:
- **functions.php**: 6 sections (MAIN, BUSINESS, OPERATIONS, MANAGEMENT, ADMINISTRATION, SETTINGS) with real URLs
- **Shortcode.php**: 7 sections (MAIN, SALES & ORDERS, INVENTORY, HR, REPORTS & DATA, WORKFLOWS, SETTINGS) with hash-based URLs

This means the sidebar looks different depending on which code path renders it.

**Recommendation:**
Consolidate to a single `dofs_get_default_sidebar_menu()` function and have the Shortcode call it.

---

### D3. Permission Check Boilerplate Across All 8 Endpoints

**Severity:** Low
**Status:** Not Fixed (Advisory)

**Problem:**
Every endpoint class has an identical `check_permission()` pattern:

```php
public function check_permission() {
    if (!is_user_logged_in()) {
        return new \WP_Error('rest_not_logged_in', __('...'), ['status' => 401]);
    }
    if (!current_user_can('CAPABILITY_NAME')) {
        return new \WP_Error('rest_forbidden', __('...'), ['status' => 403]);
    }
    return true;
}
```

**Recommendation:**
Extract a base class or trait with a reusable `check_capability($cap)` method.

---

### D4. `escapeHtml()` Duplicated in theme.js — `theme.js:471` and `theme.js:651`

**Severity:** Low
**Status:** Fixed

**Problem:**
Identical `escapeHtml()` function was defined in both `initSearch()` and `initMobileSearch()` closures.

**Fix:**
Extracted to a single top-level `escapeHtml()` function shared by both search implementations.

---

### D5. Desktop/Mobile Search Logic Duplicated — `theme.js:375-432` vs `theme.js:564-608`

**Severity:** Medium
**Status:** Fixed

**Problem:**
`performSearch()` and `performMobileSearch()` contained ~90% identical code: building the API URL, setting headers, making the fetch call, and handling errors.

**Fix:**
Extracted a shared `fetchSearchResults(query, callback)` utility. Both search functions now call it with a callback for their specific rendering logic.

---

### D6. Admin Settings Page Rendering Duplicated — `admin-settings.php`

**Severity:** Low
**Status:** Not Fixed (Advisory)

**Problem:**
`dofs_render_quick_access_page()`, `dofs_render_quick_actions_page()`, and `dofs_render_services_page()` share ~90% of their form rendering and save handling logic with only minor differences (field sets, nonce names, option keys).

**Recommendation:**
Extract a generic `dofs_render_items_settings_page($config)` function that accepts the differences as parameters.

---

### D7. Dropdown Toggle Logic Duplicated — `theme.js`

**Severity:** Medium
**Status:** Fixed

**Problem:**
`initAppLauncher()`, `initNotifications()`, and `initUserDropdown()` each had identical open/close/toggle/escape/click-outside logic (~40 lines each, ~120 lines total).

**Fix:**
Replaced all three with a single `initDropdown(containerId, toggleId, menuId)` function called three times:

```js
initDropdown('app-launcher-container', 'app-launcher-toggle', 'app-launcher-dropdown');
initDropdown('notifications-container', 'notifications-toggle', 'notifications-dropdown');
initDropdown('user-dropdown-container', 'user-dropdown-toggle', 'user-dropdown-menu');
```

---

## 4. Logic Issues

### L1. Shortcode Uses Wrong Menu Location Names — `Shortcode.php:140-143,197-201`

**Severity:** High
**Status:** Fixed

**Problem:**
The Shortcode looked for menu locations `dofs_dashboard_top` and `dofs_dashboard_sidebar`, but `functions.php:41-44` registers `dofs_topbar` and `dofs_sidebar`. The locations **never match**, so the Shortcode always falls back to hardcoded defaults.

```php
// BEFORE (wrong location names)
if (isset($locations['dofs_dashboard_top'])) {
    $location = 'dofs_dashboard_top';
}
```

**Fix:**
Added the correct registered location names as the first check, keeping legacy names as fallbacks:

```php
// AFTER (matches registered locations)
if (isset($locations['dofs_topbar'])) {
    $location = 'dofs_topbar';
} elseif (isset($locations['dofs_dashboard_top'])) {
    $location = 'dofs_dashboard_top';
} elseif (isset($locations['sfs_dashboard_top'])) {
    $location = 'sfs_dashboard_top';
}
```

---

### L2. Inconsistent Text Domains Across Files

**Severity:** Medium
**Status:** Fixed

**Problem:**
Three different text domains were used across the codebase:
- `'simple-hr-suite'` — all 8 endpoint files
- `'dofs-dashboard'` — Shortcode.php
- `'dofs-theme'` — functions.php, admin-settings.php, page templates

This breaks WordPress translation loading since only one `.pot`/`.po` file is typically loaded per text domain.

**Fix:**
Unified all text domains to `'dofs-theme'` across all 13 modified files.

---

### L3. Non-deterministic API Response — `ManagerSummary.php:144`

**Severity:** Low
**Status:** Fixed

**Problem:**
`rand(0, 59)` was used to generate mock punch times, making the API return different data on every request for the same user:

```php
// BEFORE (random on every call)
$first_punch = sprintf('%02d:%02d', max(7, min(9, $hour - rand(0, 2))), rand(0, 59));
```

**Fix:**
Made the mock data deterministic using a seed based on user ID and hour:

```php
// AFTER (deterministic per user per hour)
$seed = $user_id + $hour;
$first_punch = sprintf('%02d:%02d', max(7, min(9, $hour - ($seed % 3))), ($seed * 7) % 60);
```

---

### L4. Duplicate Search Results Possible — `GravityFormsSearch.php:178-183`

**Severity:** Medium
**Status:** Fixed

**Problem:**
When the search query was numeric, the entry was fetched both by ID lookup AND by field search. If the entry matched both criteria, it appeared twice in results.

**Fix:**
Added a `$seen_ids` deduplication map (see [P1](#p1-gravityformssearch-loads-all-entries-across-all-forms--gravityformssearchphp186) for code).

---

### L5. Inconsistent Capability Checks — `SalesOverview.php` and `ReportsOverview.php`

**Severity:** Medium
**Status:** Fixed

**Problem:**
The Shortcode grants dashboard access to users with either `dofs.view_dashboard` OR `sfs_hr.view_dashboard_manager`. However, SalesOverview and ReportsOverview only checked `sfs_hr.view_dashboard_manager`:

```php
// BEFORE (only checks one capability)
if (!current_user_can('sfs_hr.view_dashboard_manager')) {
```

A user with only `dofs.view_dashboard` could view the dashboard shell but got 403 errors when the React app fetched sales/reports data.

**Fix:**
Added both capability checks to match the Shortcode logic:

```php
// AFTER (checks both capabilities)
if (!current_user_can('dofs.view_dashboard') &&
    !current_user_can('sfs_hr.view_dashboard_manager')) {
```

---

## 5. Summary Table

| ID | Category | Severity | File(s) | Status |
|----|----------|----------|---------|--------|
| S1 | Security | Medium | `page-settings.php` | Fixed |
| S2 | Security | High | `Shortcode.php` | Fixed |
| S3 | Security | High | `GravityFormsSearch.php` | Fixed |
| S4 | Security | Medium | `functions.php` | Fixed |
| S5 | Security | Low | `functions.php` | Fixed |
| P1 | Performance | High | `GravityFormsSearch.php` | Fixed |
| P2 | Performance | High | `FlowTasks.php` | Fixed |
| P3 | Performance | Medium | `functions.php` | Advisory |
| P4 | Performance | Low | `Shortcode.php` | Advisory |
| P5 | Performance | High | `useApi.js` | Fixed |
| D1 | Duplication | Medium | `functions.php`, `Shortcode.php` | Advisory |
| D2 | Duplication | Medium | `functions.php`, `Shortcode.php` | Advisory |
| D3 | Duplication | Low | All endpoint files | Advisory |
| D4 | Duplication | Low | `theme.js` | Fixed |
| D5 | Duplication | Medium | `theme.js` | Fixed |
| D6 | Duplication | Low | `admin-settings.php` | Advisory |
| D7 | Duplication | Medium | `theme.js` | Fixed |
| L1 | Logic | High | `Shortcode.php` | Fixed |
| L2 | Logic | Medium | All endpoint files, `Shortcode.php` | Fixed |
| L3 | Logic | Low | `ManagerSummary.php` | Fixed |
| L4 | Logic | Medium | `GravityFormsSearch.php` | Fixed |
| L5 | Logic | Medium | `SalesOverview.php`, `ReportsOverview.php` | Fixed |

**Total: 16 fixed, 5 advisory (require larger refactoring)**
