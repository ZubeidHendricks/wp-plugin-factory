# Submitting to the WordPress.org plugin directory

What's automatable is done. What needs a human (your .org account + their review)
is checklisted below. Nothing here can be skipped — wordpress.org reviews every
new plugin by hand.

## ✅ Already done (in this repo)

- **Code**: 12 plugins, each self-contained, GPL-2.0, bundling the factory core.
- **Syntax**: `php -l` clean on every file (`bin/test-plugins.php` harness).
- **Runtime**: boot + render smoke test passes 12/12.
- **readme.txt**: validates against directory rules (`bin/validate-readme.php`).
- **Assets**: icon (256²/128²) + banner (772×250 / 1544×500) in each plugin's
  `.wordpress-org/` folder (`bin/generate-assets.php`).
- **Releases**: each repo has a `v1.0.0` GitHub release with an installable zip.

## ⛔ Needs you (cannot be automated)

1. **Create a WordPress.org account** → https://login.wordpress.org/register
2. **Submit each plugin for review**
   https://wordpress.org/plugins/developers/add/ — upload the plugin zip
   (from the GitHub `v1.0.0` release, or rebuild with the steps below).
   - First submission per author is reviewed manually (days → a few weeks).
   - They email you an SVN repo URL once approved.
3. **Commit to SVN** once approved:
   ```
   svn co https://plugins.svn.wordpress.org/<slug>/ <slug>-svn
   # copy plugin files into trunk/, tag/1.0.0/
   # copy .wordpress-org/* into assets/   (icon-*, banner-*)
   svn ci -m "Initial release 1.0.0"
   ```
   The `assets/` directory (icon/banner) lives at the SVN **root**, not in trunk.

## ⚠️ Still recommended before submitting

- **Screenshots** (`screenshot-1.png`, `-2.png` …) — these need a real WP install
  showing each plugin in action; they can't be generated headlessly. Add them to
  `.wordpress-org/` and reference them in readme.txt under `== Screenshots ==`.
- **Real WP install test** — the harness catches fatals, but install each zip in
  a live WP (or LocalWP / Docker `wordpress:php8.2`) and click through once.
- **Unique display names** — search the directory first; rename if a name clashes.
- **Tested up to** — bump to the current WP version at submission time.

## Rebuild a plugin zip

```bash
cd ~/wp-plugins/<wp-slug>
slug=${PWD##*/wp-}
zip -r ../$slug.zip . -x '.git/*' '.wordpress-org/*' 'README.md' 'freemius-config.sample.php' '.gitignore'
```

## Suggested order

Submit 2–3 first (e.g. Duplicate Anything, Reading Time Plus, FAQ Accordion) to
clear the manual author review, then the rest go faster.

## Name-collision check (verified 2026-06-28)

Ran `bin/check-names.php` against the wordpress.org API. All 12 slugs are now
free. Two were renamed to avoid taken slugs:

- `announcement-bar` → **`slim-announcement-bar`** ("Slim Announcement Bar")
- `testimonials-slider` → **`simple-testimonials-slider`** ("Simple Testimonials Slider")

Re-run `bin/check-names.php` before each submission in case a name gets taken.
