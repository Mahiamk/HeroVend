## HeroVend — Project Setup Log

This section documents the steps taken to bootstrap this project, from a fresh Laravel install through to a working Filament admin panel.

1. **Created the Laravel project** using the `laravel/laravel` skeleton (Laravel `^13.8`, PHP `^8.3`).
2. **Configured the environment** — copied `.env.example` to `.env` and set:
   - `DB_CONNECTION=mysql`, `DB_DATABASE=herovend`, `DB_USERNAME=root`
   - `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database`
3. **Created the `herovend` MySQL database** locally and ran `php artisan key:generate`.
4. **Ran the initial migrations** (`php artisan migrate`) — creates the `users`, `cache`, and `jobs` tables.
5. **Installed Filament v3** as the admin panel:
   ```bash
   composer require filament/filament:"^3.2" -W
   php artisan filament:install --panels
   ```
   This generated `app/Providers/Filament/AdminPanelProvider.php`, registering an admin panel at `/admin` with the Amber color scheme.
6. **Created the first admin user** via:
   ```bash
   php artisan make:filament-user
   ```
   - Name: `anwarkoji`
   - Email: `mahikomohammed@gmail.com`
   - Password: set interactively
7. **Set up the frontend build** with Vite + Tailwind CSS v4 (`npm install`, `resources/css/app.css`, `resources/js/app.js`).
8. **Installed Laravel Boost** (`composer require laravel/boost --dev`, `php artisan boost:install`) for AI-agent tooling, which added `AGENTS.md`, `boost.json`, and Cursor integration (`.cursor/mcp.json`, `.cursor/skills/`).
9. **Verified the setup** by running `composer run dev` (serves the app, queue listener, log tailing, and Vite dev server together) and logging into `/admin` with the Filament admin user created above.

### Local development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev   # server + queue + logs + vite, all at once
```

Admin panel: `http://localhost:8000/admin`

---

## Benefit Slides — Feature Build Log

This documents everything done to take the homepage "benefits carousel" from a hardcoded, unmanageable section to a fully admin-editable feature backed by the database — every command run and every file changed, in the order it happened.

### 1. Fixed a broken database schema

The `benefit_slides` table had already been migrated in this environment, but the migration file on disk only created `id` + timestamps — no `title`, `body_text`, or `sort_order` columns. Meanwhile the live DB had those columns already, but under a **different name** (`body` instead of `body_text`), out of sync with the model/seeder/Filament resource, which all expected `body_text`.

- Fixed `database/migrations/2026_07_04_164402_create_benefit_slides_table.php` to properly define `title` (string), `body_text` (text), `sort_order` (unsigned integer, default 0) — so a fresh `migrate:fresh` now builds the table correctly.
- Generated a follow-up migration to reconcile the already-migrated live DB without losing its data:
  ```bash
  php artisan make:migration rename_body_to_body_text_on_benefit_slides_table --table=benefit_slides
  ```
  Written to guard with `Schema::hasColumn(...)` so it's a no-op on fresh installs (where the base migration already creates `body_text` directly) and only renames `body` → `body_text` on the drifted live DB.
- Ran `php artisan migrate` to apply it.

### 2. Model, factory, and seeder

- `app/Models/BenefitSlide.php` — fillable `title`, `body_text`, `sort_order`; added `moveToSortOrder(int $position)`, which re-sequences every slide's `sort_order` to a unique 1..N ordering inside a `DB::transaction` (with `lockForUpdate` on the siblings) whenever a slide is created or edited, treating the submitted number as an "insert position" rather than a literal stored value — so two slides can never end up sharing the same order number.
- `database/factories/BenefitSlideFactory.php` — was an empty stub; filled in `title`, `body_text`, `sort_order`.
- `database/seeders/BenefitSlideSeeder.php` — completed the 4 slides (`LOW CAPITAL`, `ALWAYS ON`, `EASY TO SCALE`, `LOW RISK`); wrapped the create loop in `DB::transaction` so a failure partway through can't half-seed the table.
- `database/seeders/DatabaseSeeder.php` — added `$this->call(BenefitSlideSeeder::class)`.

### 3. Filament admin resource (`app/Filament/Resources/BenefitSlideResource.php`)

- **Layout**: form organized into two `Section`s — "Slide Content" (title + body) and "Display Order" (sort_order) — instead of a flat field list.
- **Rich editing**: `body_text` upgraded from a plain `Textarea` to a `MarkdownEditor` with a pared-down toolbar (no tables/headings/blockquotes/attachments — this is short marketing copy, not a blog post).
- **Validation**: `title` required, 2–60 chars, unique; `body_text` required, 10–600 chars; `sort_order` integer, min 1, max 1000, auto-defaults to `max(sort_order) + 1`.
- **Table**: drag-and-drop reorder (`->reorderable('sort_order')`, atomic via Filament's own `CanReorderRecords` trait); body-text column shows a clean plain-text preview (markdown stripped) instead of raw `**syntax**`.
- **Bulk delete** wrapped in `DB::transaction` so deleting several selected slides is all-or-nothing.
- `CreateBenefitSlide.php` / `EditBenefitSlide.php` — `afterCreate()` / `afterSave()` call `$record->moveToSortOrder(...)`; both pages override `getRedirectUrl()` to return to the slides **list** after saving (Filament's defaults would otherwise stay on the edit page, or send a newly created slide to its own edit page).

### 4. Admin login & access control

- `config/app.php` — added `admin_emails`, sourced from a comma-separated `ADMIN_EMAILS` env var.
- `.env` / `.env.example` — added `ADMIN_EMAILS=mahikomohammed@gmail.com`.
- `app/Models/User.php` — implements `FilamentUser`; `canAccessPanel()` only allows emails in `config('app.admin_emails')`, so the panel isn't open to every registered user.
- Created the admin login via:
  ```bash
  php artisan tinker --execute '
  App\Models\User::updateOrCreate(
      ["email" => "mahikomohammed@gmail.com"],
      ["name" => "Admin", "password" => "root@123", "email_verified_at" => now()]
  );'
  ```

### 5. Wiring the homepage to the database (`resources/views/welcome/features.blade.php`, `routes/web.php`)

- The `/` route now fetches `BenefitSlide::orderBy('sort_order')->get()`, wrapped in a try/catch (`QueryException` → log + fall back to an empty collection, so a DB hiccup shows an empty section instead of a 500 page).
- Replaced 4 hardcoded slides in the Blade view with a `@foreach ($slides as $slide)` loop.
- Preserved the original Figma-exact layout while making it dynamic:
  - Title sits in a fixed `328.88×118px` centered flex box so titles of any length stay vertically centered instead of drifting.
  - `max-w-[329px] break-words` so long titles wrap onto more lines instead of overflowing.
  - Multi-word titles keep the original design's "last word on its own line with a leading hyphen" treatment (e.g. `Restocking & Inventory` / `-Tools`), generalized via `preg_split` instead of hand-written per-slide `<br>` tags.
  - Body text rendered via `Str::of($slide->body_text)->markdown()`; paragraph spacing handled with Tailwind `[&>p]` arbitrary-variant selectors.
  - Empty-state fallback ("Benefit highlights are coming soon.") with the slider controls hidden when there are zero slides — an admin clearing every slide doesn't break the layout.
- The `/` response is sent with `Cache-Control: no-store, must-revalidate` so a browser refresh (including via the back/forward button) always reflects the latest admin edit instead of showing a cached snapshot.

### 6. Image optimization

- Converted the two directly-used PNGs to WebP via `npx --yes sharp-cli`, wired into `<picture>` elements with the original PNG as fallback:
  - `image-69.png` (hero background, LCP element): 2.4 MB → 120 KB WebP, `fetchpriority="high"`.
  - `image 69.png` (stats bar background): 220 KB → 16 KB WebP, `loading="lazy"`.
- Discovered `image 83.svg`, `image 83-2.svg`, and `image 80.svg` were Figma exports that just wrap one giant base64-encoded PNG inside an `<image>` tag (inflated ~33% by base64 + SVG overhead). Wrote a one-off PHP script to extract the embedded raster, recompress it to WebP via `sharp-cli`, and re-inject it as a new base64 data URI **in the same SVG structure** — same shapes/transforms/clip-paths, just a much lighter payload (verified with a diff that only the base64 data changed). Result: ~502 KB → ~80 KB and ~1.0 MB → ~72 KB.
- Added `loading="lazy"` / `decoding="async"` to the below-the-fold product images in `page3-1.blade.php` and `page3-2.blade.php`.
- Total page image weight for the affected assets: **~4.7 MB → ~358 KB (~92% reduction)**.
- Left `cards-state-1.svg`, `cards-state-2.svg`, `benefits-slide-1.svg`, `benefits-slide-2.svg` (~5.4 MB) in place but flagged as unused/dead — not referenced by any page.

### 7. Fixed a live duplicate `sort_order` bug

Found the live DB actually had two slides sharing `sort_order = 1` (from earlier manual testing). The `moveToSortOrder()` fix above (section 2/3) prevents this going forward; the existing bad data was repaired directly via `php artisan tinker` by re-numbering all slides sequentially by their current order.

### 8. Live-update experiment (built, then reverted)

Initially explored making the homepage section update automatically without a manual refresh, by converting it to a polling Livewire component (`App\Livewire\BenefitSlidesSection`, `wire:poll.5s`). Per a follow-up request to keep it simple, this was **fully reverted**: the Livewire component and its view were deleted, `features.blade.php` and `routes/web.php` were restored to the plain `$slides`-driven Blade loop, and `@livewireStyles`/`@livewireScripts` were removed from the layout. The homepage now updates the same way it always did — on refresh — but the `Cache-Control: no-store` header (section 5) ensures a refresh is never served stale content from the browser cache.

### 9. Test suite

- Re-enabled `RefreshDatabase` in `tests/Pest.php` — it had been commented out, which silently broke every feature test that touched the database (the sqlite test DB had no tables at all).
- Added `tests/Feature/BenefitSlideManagementTest.php`, covering: guest/non-admin panel access control, listing, create/edit/delete/bulk-delete, drag-and-drop reorder, `sort_order` collision handling on both create and edit, the homepage's empty-state fallback, sort-order rendering, and the no-store cache header. 15 tests, all passing.
- Every change in this log was run through `vendor/bin/pint --format agent` and `php artisan test --compact` before being considered done.

---

## Admin Branding & Landing Page SEO — Feature Build Log

This documents the work to replace default "Laravel" branding with "HeroVend" across the app, and to make the public landing page (`/`) SEO- and share-ready.

### 1. Admin panel branding & layout

- `app/Providers/Filament/AdminPanelProvider.php` — added `->brandName('HeroVend')` (the sidebar was showing "Laravel" because Filament falls back to `config('app.name')`, which was still the Laravel default) and `->sidebarWidth('14rem')` (down from Filament's 20rem default) so the Benefit Slides table columns fit without horizontal scrolling.
- `.env` — `APP_NAME=Laravel` → `APP_NAME=HeroVend`. This also fixes the browser tab title and the mail "from" name, both of which read `config('app.name')`.

### 2. Favicon, generated from the existing logo mark

No dedicated favicon existed; the browser tab just showed the generic default. Rather than commission new art, the "V" mark was extracted from the wordmark already used in the hero header (`public/images/frame-2147226374.svg`, first `<path>` only) into a new square icon:

- `public/images/favicon-source.svg` — the V mark, white, on a rounded square in the brand accent color (`#FF5A1F`, from `--color-accent` in `resources/css/app.css`).
- Rasterized to PNG/ICO with PHP's `Imagick` extension (no CLI image tools were available in this environment): `public/favicon.ico` (multi-res 16/32), `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png` (180px), `public/images/icon-192.png` / `icon-512.png` (for the web manifest).
- `public/site.webmanifest` — PWA metadata (name, icons, theme/background color).
- All linked from `resources/views/welcome.blade.php`'s `<head>`.

### 3. SEO & social sharing meta (`resources/views/welcome.blade.php`)

- Real `<title>` and `<meta name="description">` (previously just `config('app.name', 'HeroVend')`).
- `<link rel="canonical">`, `<meta name="robots" content="index, follow">`.
- Open Graph (`og:type`, `og:title`, `og:description`, `og:image` + dimensions) and Twitter Card (`summary_large_image`) tags, so links shared in Slack/Twitter/iMessage render a title, description, and image instead of a bare URL.
- `public/images/og-image.jpg` — generated from the existing hero photo (`image-69.png`), center-cropped to the standard 1200×630 OG size via `Imagick::cropThumbnailImage()`, then re-encoded as JPEG (quality 82) to shrink it from a ~1 MB PNG crop down to ~108 KB.
- JSON-LD `Organization` structured data in a `<script type="application/ld+json">` block.
  - **Gotcha**: writing the array key as `'@context'` directly in the Blade template broke the page — Laravel's `@context` Blade directive (for the `Context` facade) matches literally on `@context` anywhere in the raw template text, including inside a PHP string, and tries to compile it as a directive. Fixed by escaping it as `'@@context'` / `'@@type'`, which Blade turns into a literal `@` in the output.

### 4. `robots.txt` and `sitemap.xml`, served as routes (not static files)

Both need an absolute, environment-correct URL (from `config('app.url')`), which a static file in `public/` can't provide — and a static file there would also take precedence over any route of the same name, so both were implemented as routes instead:

- `routes/web.php` — `GET /robots.txt` (plain text, `Sitemap:` line points at `/sitemap.xml`) and `GET /sitemap.xml` (renders `resources/views/sitemap.blade.php`), both resolving URLs via `url('/')` so they automatically point at the right domain once `APP_URL` is set for production.
- **Gotcha**: a literal `<?xml version="1.0" encoding="UTF-8"?>` at the top of a `.blade.php` file breaks it. Blade's compiler scans the raw template text for `<? ... ?>`-looking spans before compiling `{{ }}`/directives; it either passes a self-contained `<?xml...?>` straight through uncompiled (so the literal Blade syntax ends up in the response and PHP's own tokenizer then tries to parse `<?xml` as a real (short) open tag — `syntax error, unexpected identifier "version"`), or, if the closing `?>` is split away from the opening `<?`, treats everything up to the next `?>` anywhere later in the compiled file (even Blade's own auto-appended `ENDPATH` comment) as one giant unprocessed region, breaking every directive in the view. The fix is to emit the declaration from genuine PHP tags — `<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>` — since the `<?xml...?>` text then lives inside a quoted PHP string, which PHP's tokenizer doesn't reinterpret as a tag.

Verified by starting a local server and curling `/`, `/robots.txt`, `/sitemap.xml`, and every generated asset for a `200`.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
