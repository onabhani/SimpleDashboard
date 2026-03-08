# DOFS System Dashboard

Internal dashboard theme — actively used in production at dofs.daralmasalla.com. The React dashboard widget is the newest addition; the rest of the theme is stable.

WordPress theme powering an internal business dashboard for Dar Al Masalla (DOFS), built on Tailwind CSS with Gravity Forms/GravityView/Gravity Flow integration for workflow management.

## Commands

- **Dev**: `cd assets/dashboard && npm run dev` (Vite dev server for React dashboard widget)
- **Build**: `cd assets/dashboard && npm run build` (outputs to `assets/dashboard/build/`)
- **Deploy**: manual — upload theme to `wp-content/themes/` on ScalaHosting; do not automate
- **Cache clear**: `wp cache flush` (WP-CLI on server)

## Dependencies

- **Required:** Gravity Forms, Gravity Flow, GravityView
- **Required plugins on the stack:** Simple HR Suite (REST data for HR widgets), Simple Notifications (bell system), GravityFlow Reports
- **Dev:** Node.js + npm (for React dashboard widget build only)

## Architecture

The theme uses a WordPress template hierarchy with a persistent header/sidebar layout defined in `header.php`, `sidebar.php`, and `footer.php`. Business logic lives in `functions.php` (sidebar menu, workflow tasks, icons, settings) and `includes/Dashboard/` (REST API endpoints under `SFS_HR\Dashboard` namespace). Admin settings are in `inc/admin-settings.php`.

- **Theme entry**: `style.css` (theme metadata) + `functions.php` (setup, hooks, helpers)
- **Templates**: `front-page.php`, `page.php`, `single.php`, `archive.php`, `search.php`, `page-section.php`, `page-settings.php`, `page-help.php`, `404.php`
- **REST endpoints**: `includes/Dashboard/Endpoints/` — `FlowTasks`, `GravityFormsSearch`, `HrRequests`, `ManagerSummary`, `ManagerTeam`, `MyHrStatus`, `ReportsOverview`, `SalesOverview`
- **REST namespace**: `sfs-hr/v1/dashboard`
- **Admin settings**: `inc/admin-settings.php` (Quick Access, Quick Actions, Services config pages)
- **CSS**: `assets/css/theme.css` (custom styles on top of Tailwind CDN)
- **JS**: `assets/js/theme.js` (sidebar, search, dropdowns, dark mode)
- **DB migrations**: none — all data stored in Gravity Forms entries and WordPress options (`dofs_*` option keys)

### React Dashboard Widget

- **Location**: `assets/dashboard/`
- **Stack**: React + Vite, built to `assets/dashboard/build/dashboard.js`
- **Consumes**: REST endpoints from `sfs-hr/v1/dashboard` namespace
- **Enqueued**: only on the front page via `wp_enqueue_script` in `functions.php`

## Boundaries

- **Never query `wp_gf_entry` or `wp_gf_entry_meta` directly** — always use `GFAPI::get_entries()`, `GFAPI::get_entry()`, and related methods
- **Never modify Gravity Flow step configurations programmatically** — step IDs and settings are managed in WP admin
- **GravityView templates are managed by the GravityView plugin** — do not duplicate or override `gf_entry_detail` templates

## Domain Terminology

| Term | Meaning |
|------|---------|
| Entry | A Gravity Forms submission (row in `gf_entry`) |
| Workflow | A Gravity Flow process attached to a form with sequential approval/action steps |
| Step | A single stage in a Gravity Flow workflow (e.g. approval, user input, notification) |
| Inbox | Gravity Flow's task queue showing entries pending action by the current user |
| Section | A dashboard area grouping related pages (e.g. "Sales & Orders", "Production") |
| Services | External tool links shown in the header app launcher grid |
| Quick Access | Configurable shortcut links on the dashboard homepage |
| Quick Actions | Configurable action buttons on the dashboard homepage |

## Conventions

- **Language in code**: English
- **Language in UI strings**: English (translatable via `dofs-theme` text domain; site also serves Arabic users)
- **Function prefix**: `dofs_` for all theme functions
- **Class namespace**: `SFS_HR\Dashboard` for REST API and module classes
- **CSS classes**: Tailwind utility-first; custom classes in `assets/css/theme.css` use BEM-like naming for Gravity Forms/Flow overrides
- **Icon system**: `dofs_icon($name, $class)` helper returns inline SVGs — add new icons to the `$icons` array in `functions.php`
- **Settings storage**: WordPress options API with `dofs_` prefix (e.g. `dofs_quick_access_items`, `dofs_services_items`)
- **Template pattern**: Every page template calls `get_header(); get_sidebar();` then opens `<main class="flex-1 overflow-y-auto">`, closed in `footer.php`

## Known Gotchas

- **Namespace mismatch**: REST classes use `SFS_HR\Dashboard` namespace (legacy from HR Suite origins), not `DOFS`. Do not rename — it would break autoloading and endpoint registration
- **Gravity Forms CSS specificity**: GF and Gravity Flow inject their own styles. Theme overrides in `theme.css` often need `!important` and highly specific selectors (e.g. `.gform_wrapper .gravityflow-field-value`)
- **Sidebar positioning**: On desktop the sidebar uses `position: relative` within a flex container (not `fixed`/`sticky`); on mobile it uses `fixed` with translate transforms. The collapse state is stored in `localStorage` key `dofs-sidebar-collapsed`
- **Dark mode**: Controlled via `localStorage` key `dofs-theme` and the `dark` class on `<html>`. Every new component must include dark mode variants
- **Admin bar offset**: When logged into WP, the admin bar shifts the layout. CSS accounts for this with `body.admin-bar` selectors
- **Tailwind is loaded via CDN** — not compiled locally. Custom utilities that Tailwind doesn't know about are defined in `theme.css`
- **REST API auth**: Endpoints use `X-WP-Nonce` header for authentication; the nonce is localized via `dofsTheme.nonce` in JS

## Workflow Rules

- Always prefix new PHP functions with `dofs_`
- Always include `dark:` variants when adding new Tailwind-styled components
- Never modify Gravity Flow step settings without confirming step ID with the user
- After changing admin settings structure, verify the settings page still saves/loads correctly in `inc/admin-settings.php`
- When adding new REST endpoints, register them in `includes/Dashboard/RestController.php`
- When adding new icons, add SVG path to the `$icons` array in the `dofs_icon()` function in `functions.php`
