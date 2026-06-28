# WP Plugin Factory

A factory for shipping **100 simple, monetizable WordPress plugins** on a single
shared core — the WordPress equivalent of an app-factory portfolio play.

Each plugin is its own repo and its own free listing on wordpress.org, but they
all bundle the same `core/` mini-framework so licensing, settings UI, and the
freemium paywall are written **once**.

## What's here

```
core/factory-core.php   Shared mini-framework (bootstrap, settings, Pro gating)
template/               Boilerplate plugin with {{TOKENS}}
bin/new-plugin.sh       Generator: stamp a new plugin repo from the template
ROADMAP.md              The 100-plugin roadmap + status
```

## Monetization model

- **Free** version on the wordpress.org repo — your distribution & SEO engine.
- **Pro** license ($29–$99/yr) sold via [Freemius](https://freemius.com) or
  EDD + Software Licensing — handles payments, tax/VAT, trials, affiliates.
- Pro features are gated with one filter: `add_filter( '{slug}_is_pro', '__return_true' )`.
  Freemius flips this automatically on a valid license.

## Spin up a new plugin

```bash
bin/new-plugin.sh "Reading Time Plus" reading-time-plus
# → creates ../wp-reading-time-plus with core bundled, ready to push
```

## Shipped so far

| Plugin | Repo | Category |
|--------|------|----------|
| Duplicate Anything | `wp-duplicate-anything` | Admin workflow |
| Coming-Soon & Maintenance Mode | `wp-coming-soon-lite` | Admin workflow |
| Disable Comments Clean | `wp-disable-comments-clean` | Admin workflow |
| Reading Time Plus | `wp-reading-time-plus` | Content / SEO |
| Announcement Bar | `wp-announcement-bar` | Content / display |
| WhatsApp Chat Button | `wp-whatsapp-chat-button` | Integration |
| Countdown Timer | `wp-countdown-timer-block` | Content / display |
| FAQ Accordion | `wp-faq-accordion-block` | Content / SEO |

See [ROADMAP.md](ROADMAP.md) for all 100.

## License

GPL-2.0-or-later — required for distribution on wordpress.org.
