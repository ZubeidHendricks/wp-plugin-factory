# The 100-Plugin Roadmap

A factory play: one shared core, 100 sharp single-purpose plugins, freemium
monetization (free on wordpress.org → Pro license via Freemius/EDD).

Status legend: ✅ shipped · 🛠 in progress · ⬜ planned

## WooCommerce (highest willingness-to-pay)
1. ⬜ Min/max order quantity rules
2. ⬜ Conditional shipping by cart contents
3. ⬜ Bulk price editor (scheduled sales)
4. ⬜ Frequently-bought-together upsells
5. ⬜ Role-based / wholesale pricing
6. ⬜ Abandoned-cart email recovery (lite)
7. ⬜ Custom order status manager
8. ⬜ Product badges (sale/new/low-stock)
9. ⬜ Checkout field editor
10. ⬜ BOGO / tiered discounts

## Forms & lead capture
11. ⬜ Multi-step wrapper for CF7
12. ⬜ Form-to-PDF generator
13. ⬜ Save-and-resume form progress
14. ⬜ Form entry CRM / CSV export
15. ⬜ Conditional logic add-on
16. ⬜ Signature field add-on
17. ⬜ Form-to-Google-Sheets sync
18. ⬜ Quote / price calculator fields

## Admin & workflow savers
19. ✅ Duplicate Anything — `wp-duplicate-anything`
20. ⬜ Bulk find-and-replace in DB
21. ⬜ Scheduled publish/unpublish/expire
22. ⬜ Custom admin columns manager
23. ⬜ Login page customizer
24. ✅ Coming-Soon & Maintenance Mode — `wp-coming-soon-lite`
25. ⬜ Media library folders
26. ✅ Disable Comments Clean — `wp-disable-comments-clean`
27. ⬜ Admin dark mode + UX tweaks
28. ⬜ Activity log / audit trail
29. ⬜ Last-modified + author display
30. ⬜ Hide/rearrange admin menu by role

## SEO & marketing micro-tools
31. ⬜ Broken link checker (async)
32. ⬜ Auto internal-linking
33. ⬜ Schema / structured-data injector
34. ✅ Redirect Manager — `wp-redirect-manager`
35. ⬜ Open Graph / social meta editor
36. ⬜ Table of contents generator
37. ✅ Reading Time Plus — `wp-reading-time-plus`
38. ⬜ UTM link builder + click tracker
39. ✅ Cookie Consent Bar — `wp-cookie-consent-bar`
40. ⬜ Exit-intent popup

## Content & display widgets
41. ✅ FAQ Accordion (+ schema) — `wp-faq-accordion-block`
42. ✅ Pricing Table — `wp-pricing-table-block`
43. ✅ Simple Testimonials Slider — `wp-simple-testimonials-slider`
44. ⬜ Before/after image comparison
45. ✅ Countdown Timer — `wp-countdown-timer-block`
46. ⬜ Tabs / toggle block
47. ⬜ Business hours / "open now"
48. ⬜ Team / staff directory
49. ⬜ Logo carousel
50. ✅ Slim Announcement Bar — `wp-slim-announcement-bar`

## Integrations & automation bridges
51. ⬜ WP → Slack/Discord/Telegram
52. ⬜ WP → Airtable/Notion sync
53. ⬜ Stripe payment button (non-Woo)
54. ⬜ Mailchimp/ConvertKit signup
55. ✅ WhatsApp Chat Button — `wp-whatsapp-chat-button`
56. ⬜ Google Reviews display
57. ⬜ Currency switcher (live rates)
58. ⬜ WP → Zapier/webhook hub

## Scaling 58 → 100 (multiplication, not invention)
- Port each WooCommerce idea to **Easy Digital Downloads** (+10)
- Re-skin each form add-on for **Gravity Forms / WPForms / Forminator** (+15)
- Ship each display widget as both a **Gutenberg block** and **Elementor widget** (+15)
- Bundle "Pro" packs that combine 3–5 related plugins (+ revenue, same code)

Each plugin = `core` + one feature. Realistically a 1–3 day build on the scaffold.
