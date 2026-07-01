# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **PrestaShop 1.7/8 e-commerce store** for Ceramic Connection (ceramicsdi), a ceramics retailer. The project runs locally on WAMP (Windows + Apache + MySQL + PHP) at `c:\wamp64\www\ceramicsdi`.

The store is customized through:
1. **Overrides** (`/override/`) — PHP class/controller extensions using PrestaShop's override system
2. **Custom theme** (`/themes/child_classic/`) — child theme inheriting from PrestaShop's `classic` theme
3. **Custom modules** (`/modules/`) — a mix of custom-built and third-party modules

## Development Commands

```bash
# Install PHP dependencies
make composer      # runs: composer install

# Build theme assets (SCSS → CSS)
make assets        # runs: ./tools/assets/build.sh

# Full install (composer + assets)
make install
```

Theme SCSS is compiled from `themes/child_classic/assets/css/custom.scss` → `custom.css`. After editing SCSS, rebuild assets.

## Architecture

### Override System

PrestaShop overrides extend core classes without modifying core files. All overrides are in `/override/` and mirror the core directory structure:

- `override/classes/controller/FrontController.php` — **Central hub** for all frontend. Defines all shared constants (feature IDs, category IDs, country IDs) used across templates and modules. The `assignGeneralPurposeVariables()` method pushes all constants to Smarty on every page load.
- `override/classes/Cart.php` — Custom cart quantity logic with address handling
- `override/classes/Carrier.php` — Custom cheapest delivery option calculation (integrates MBE Shipping)
- `override/classes/Product.php` — Additional product methods (image by position, features, video route)
- `override/classes/order/Order.php` — Custom invoice numbering
- `override/classes/checkout/CheckoutAddressesStep.php` — Address validation at checkout
- `override/classes/checkout/CheckoutPaymentStep.php` — Payment step customization
- `override/controllers/front/ProductController.php` — Product page with custom URL pattern matching by `link_rewrite`
- `override/controllers/front/CategoryController.php`, `CartController.php`, etc.

**When adding new shared constants** (feature IDs, category IDs), add them to `FrontController.php` and call `$this->context->smarty->assign()` in `assignGeneralPurposeVariables()`.

### Theme

Child theme (`themes/child_classic/`) inherits from `classic` with `use_parent_assets: false` — it manages its own asset loading entirely.

Key files:
- `assets/css/custom.scss` — All custom styles (SCSS with variables defined at top)
- `assets/js/custom.js` — Frontend JS
- `templates/catalog/product.tpl` — Product page template
- `templates/catalog/listing/category.tpl` — Category listing
- `templates/_partials/header.tpl`, `footer.tpl` — Layout partials

Templates use Smarty 3 syntax and have access to all variables assigned in `FrontController::assignGeneralPurposeVariables()`.

### Key Constants (defined in FrontController.php)

Product features drive much of the display logic. Important feature IDs:
- `FEATURE_TIPOLOGIA_PRECIO_ID = '16'` — Pricing type (per m² vs per piece)
- `FEATURE_M2_CAJA_ID = '17'`, `FEATURE_PIEZAS_CAJA_ID = '18'` — Box quantities
- `FEATURE_SHOW_STOCK = '55'`, `FEATURE_CUSTOM_STOCK = '76'` — Stock display control
- `FEATURE_SAMPLE_AVAILABLE = '56'` — Sample availability
- `FEATURE_METROS_LINEALES = '82'` — Linear meters feature

### Custom Modules

**Custom-built modules** (not standard PrestaShop):
- `ccproductreviews` — Product review system with Google Product Reviews XML feed
- `ccfreesamplediscount` — Free sample discount logic
- `ccdesistimiento` — Withdrawal/return form module (legal requirement in Spain)
- `addsampleoncatalog` — Sample ordering from catalog pages
- `customrelatedproducts` — Custom related products logic
- `shippingcalculator` — Shipping cost calculator
- `inspiration` — Inspiration/gallery content module
- `inspirationcardsmodule` — Inspiration cards display
- `planatec` — Format/tile display with custom DB table (`ps_planatec_formatos`)
- `planatec_recomendaciones` — Product recommendations by page/section/apartment type
- `updatecatalog` — Catalog price/data update tool (admin)
- `manomanoorders` — ManoMano marketplace order integration
- `outvio` — Outvio shipping integration (carrier selection with map)
- `mbeshipping` — MBE Shipping carrier integration
- `redsyspur` — Redsys payment gateway (Spanish banking)
- `revolutpayment` — Revolut payment integration
- `seur` / `seurcashondelivery` — SEUR carrier integration

**Key third-party modules**: `ps_facetedsearch` (layered navigation), `ets_megamenu`, `ybc_blog`, `productcomments`, `ps_checkout` (PayPal), `psgdpr`, `lgcanonicalurls`, `prettyurls`.

### Database Conventions

- Uses PrestaShop's `Db::getInstance()` for queries
- Table prefix: `_DB_PREFIX_` constant (typically `ps_`)
- Custom tables: `ps_planatec_formatos`, tables created by custom modules via `install.sql` files

### Multi-language Setup

The store is multi-language (ES, FR, EN, DE, PT, NL). Language-specific feature values and pricing type strings are defined as constants in `FrontController.php`. When adding language-dependent logic, check for per-language constants there.

### URL Structure

Product URLs use a custom pattern matched by `ProductController::init()`:
```
/[id_product]-[link_rewrite].html
```

The override extracts `link_rewrite` from the URL via regex and resolves `id_product` via DB lookup, enabling SEO-friendly URLs.

## Configuration

- `config/settings.inc.php` — DB credentials (gitignored)
- `app/config/parameters.yml` — Symfony parameters (gitignored)
- `phpstan.neon.dist` — PHPStan configured at level 4, analyzing `src/` and `webservice/` paths

## Important Notes

- After modifying any file in `override/`, PrestaShop caches the compiled override. In dev: clear the cache via Admin → Advanced Parameters → Performance → Clear Cache, or delete `cache/class_index.php`.
- Template cache lives in `themes/child_classic/cache/` (gitignored). Clear from Admin or delete contents.
- The `old/` and `_old_*` directories throughout the codebase contain superseded versions — treat them as reference only, not active code.
