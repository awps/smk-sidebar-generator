# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SMK Sidebar Generator is a WordPress plugin that allows users to create unlimited sidebars and conditionally display them on any page without coding. Available on the WordPress Plugin Repository.

**Requirements:** WordPress 4.0+, PHP 5.6+

## Development Commands

```bash
# Install dependencies
composer install

# PHP syntax linting
./vendor/bin/phplint --no-cache

# Code standards check (PHPCS with HumanMade + WordPress Security standards)
phpcs

# Start local WordPress environment (localhost:8000)
docker-compose up
```

## Architecture

The plugin source lives in `src/` and follows an OOP structure:

- **`smk-sidebar-generator.php`** - Entry point. Defines helper functions (`smk_sidebar()`, shortcode), loads all classes, registers conditions
- **`abstract.php`** - `Smk_Sidebar_Generator_Abstract` base class. Handles WordPress hooks, sidebar registration, widget operations, v2 migration
- **`render.php`** - `Smk_Sidebar_Generator` extends Abstract. Admin UI, AJAX handlers, settings rendering
- **`apply.php`** - `Smk_Sidebar_Generator_Apply` class. Implements `sidebars_widgets` filter to conditionally replace sidebars
- **`condition.php`** - Abstract `Smk_Sidebar_Generator_Condition` base for condition system
- **`condition-cpt.php`** - `Smk_Sidebar_Generator_Condition_Cpt` implements post type/taxonomy conditions
- **`html.php`** - `Smk_Sidebar_Generator_Html` utility class for generating form inputs

### Extending Conditions

Register custom conditions via `smk_register_condition($class_name)`. Create a class extending `Smk_Sidebar_Generator_Condition` with a `$type` property and required methods.

## Deployment

Releases are triggered by semantic version git tags (e.g., `3.5.2`). GitHub Actions runs `deploy.sh` which pushes to WordPress.org SVN.

## Key Functions

```php
smk_sidebar($id)              // Display sidebar by ID
[smk_sidebar id="sidebarID"]  // Shortcode
smk_get_all_sidebars()        // Get all registered sidebars
smk_register_condition()      // Register custom condition class
```