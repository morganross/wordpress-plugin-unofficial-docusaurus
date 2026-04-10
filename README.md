# Unofficial Docusaurus for WordPress

This is a standalone WordPress plugin that borrows the useful parts of a docs site without shipping a separate public website shell.

WordPress keeps the real page, theme, header, footer, and menus.

The plugin provides:

- a shortcode-based docs mount
- a docs registry from markdown files
- server-side docs rendering
- a small React docs UI bundle for sidebar and page enhancements
- built assets shipped with the plugin

## Shortcode

Use this shortcode on any normal WordPress page:

```text
[acm_docs]
```

Optional shortcode attributes:

```text
[acm_docs slug="help/getting-started" title="Docs"]
```

## Project Structure

- `unofficial-docusaurus.php` is the plugin bootstrap
- `includes/` contains the PHP plugin code
- `docs/` contains the markdown docs source
- `assets/docs-app/` contains the built frontend assets shipped with the plugin
- `ui/` contains the React/Vite source used to build the docs UI

## Build

The plugin ships with prebuilt assets.

If you want to rebuild the docs UI locally:

```bash
cd ui
npm install
npm run build
```

The build output goes into `assets/docs-app/`.

## Goal

This plugin is trying to keep the WordPress shell while giving the site a docs experience with:

- a sidebar
- previous and next links
- markdown docs content
- room for client-side search later

It is not trying to host the whole Docusaurus website inside WordPress.
