# ImpressPages (PHP 8 revival)

This is a community fork of [ImpressPages](https://github.com/impresspages/ImpressPages), whose last release (5.0.3) dates from 2017 and does not run on modern PHP. The `php8` branch brings it up to date while keeping the existing API, themes and plugins working.

**Current version:** 5.2.0.

**Requirements:** PHP 8.1 or newer (tested on PHP 8.5) and MySQL or MariaDB.

### What changed in 5.2.0

- Fresh installs work on PHP 8.2 and newer again (the installer stopped at the Database step because of `utf8_decode()`).
- The installer no longer shows the generated administrator password to visitors.
- SEO and GEO in the core: `sitemap.xml`, `robots.txt` and `llms.txt` (and an optional `llms-full.txt`) built from the published pages; schema.org JSON-LD for the site owner, website and breadcrumbs; a switch per AI crawler (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, CCBot). Settings are in Config.
- Admin usability, from a heuristic audit:
  - "Saving… / Saved / Couldn't save" messages everywhere, failed saves are no longer silent, Config saves after a pause and says so.
  - A styled confirmation dialog instead of the browser's `confirm()` box, and a prompt before unsaved changes are thrown away (switching page, closing a dialog, closing Design → Options).
  - The content editor shows whether a page has unpublished changes, and says that theme texts are saved straight to the live site.
  - New navigation: an icon rail, a section column and screen lists. Outline icons, one button style, readable contrast, keyboard access to collapsible sections, names for icon-only buttons.
  - Page properties has one Save button that stays visible; Design → Options can be widened; the Log shows readable context; System → Update links asks first and is a POST.
  - Works on phones and tablets: no admin screen scrolls sideways at 375, 768, 1024 or 1366 px.
- PHP 8 notices no longer appear inside Grid edit dialogs.

After updating, run the update script (Admin → System) if it is offered: it records the new version and makes browsers load the new admin files.

### What changed from 5.0.3

- Dependencies: Zend Framework i18n/service manager replaced by Laminas, PHPMailer 5.2 upgraded to 6.x, psr/log 1 to 3 supported.
- The Aura Router 2 dependency is replaced by a small built-in router with the same route syntax (`{param}`, optional `{/a,b}` segments, `where` tokens, GET/POST restriction, named URLs).
- The bundled less.php 1.7 is replaced by the maintained `wikimedia/less.php`; the bundled phpseclib is removed and download signatures are checked with PHP's own OpenSSL.
- PHP 8.x fixes: removed the reserved `String` class, declared dynamic properties, explicit nullable parameters, `(float)` casts, null-safe escaping helpers, `E_STRICT` removed, a crash in the translation loader and the DB exception class, and log messages trimmed to fit their column.
- The installer and admin no longer contact impresspages.org (the statistics service and tracking frames are gone; the server is offline).
- The start-pack `index.php` no longer swallows requests to `index.php` when using PHP's built-in web server.
- The home page canonical link no longer has a doubled slash (`https://example.com//`).

The original README follows.

---

# ImpressPages core
This repository is used as a library for the main https://github.com/impresspages/ImpressPages respository. Here are stored the most fundamental features of ImpressPages. In long run, this repository should become into a self sustainable ImpressPages-framework without CMS.

## Installation

If you want to use the latest version from github, please follow these steps to get everything up and running.

1. Create a file called `composer.json` and put the following content in it:

```php
{
    "require": {
        "impresspages/impresspages": "5.0.*",
        "impresspages/plugin-google-analytics": "*"
    },
    "scripts": {
        "post-install-cmd": ["php vendor/impresspages/impresspages/bin/setup.php public"],
        "post-update-cmd": ["php vendor/impresspages/impresspages/bin/setup.php public"]
    },
    "autoload": {
        "psr-4": {"Plugin\\": "public/Plugin/"}
    }
}
```

2. Install all composer dependencies by running `composer install`

3. Start a webserver to serve the `public` directory. You can also use the built-in PHP webserver, switch into the public directory `cd public` and start the webserver using this command: `php -S localhost:8000 index.php`

4. Open your webserver and navigate to whatever address you are using, for example `http://localhost:8000`.

5. Follow the setup wizard.
