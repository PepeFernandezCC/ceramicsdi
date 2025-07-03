# Changelog

### 3.3.3

- Fix for older PHP (7.x) versions

### 3.3.2

- PrestaShop V9 Beta requirements

### 3.3.1

- Add id_address_delivery and id_address_invoice to cart-table at order creation

### 3.3.0

- New feature to import specific price table values

### 3.2.9

- New field "Return Tracking Code" at order management to submit to Channable

### 3.2.8

- Added additional logging for debugging
- Compatibility update (PS 8.2.0)

### 3.2.7

- Added filter possibility for order grid

### 3.2.6

- Compatibility update for 3rd-party "wkproductcustomfield" plugin latest version
- New setting option to use phone number as mobile number 

### 3.2.5

- HTTP Status codes optimized

### 3.2.4

- Optimized grid view hook handling for older PS versions (<= 1.7.6.x)

### 3.2.3

- Adaption at feed generation for outdated PHP versions (7.0/7.1/7.2)

### 3.2.2

- Fix feed generation

### 3.2.1

- Fix vulnerability, thanks to TouchWeb.fr & 202 Ecommerce

### 3.2.0

- New feature "map different shipping status per marketplace"
- Workarround for miscalculated raw prices in specific scenarios

### 3.1.9

- Added additional hooks for order creation (channableOrderCreation)
- Compatibility Update

### 3.1.8

- Added optional stock sync for multishop environments

### 3.1.7

- Added additional information shipping -> "pickup point name" for created orders
- Added additional information shipping -> "shipping center id" for created orders

### 3.1.6

- Compatibility PrestaShop 8.1.3
- New configuration option "Default string for orders with empty name fields"

### 3.1.5

- Reformat submitted phone numbers to not trigger PrestaShop internal validation on order creation

### 3.1.4

- Added support for new information "shipment_method" in Order submissions
- PS Compatibility update

### 3.1.3

- Change in customer temporary password generation for validation rule

### 3.1.2

- Fix for missing possible attribute image
- Compatibility jsonEncode & jsonDecode

### 3.1.1

- Warehouse config var fix for PrestaShop >= 1.7.8

### 3.1.0

- Compatibility PrestaShop 8.1
- Added additional hooks for feed generation (channableSql, channableAddProductToFeedCheck)

### 3.0.2

- Compatibility 8.0.2
- Implementation compatibility with pm_advancedpack plugin

### 3.0.1

- Zalando Information import feature
- Bugfix in feed SQL generation

### 3.0.0

- PrestaShop V8 Compatibility

### 2.9.3

- Fix for activated cache option when unexpected null values occur

### 2.9.2

- Added special chars replacement to fulfill PrestaShop internal string validation for customer names in order creation

### 2.9.1

- Added new option to deactivate variants in feed at all

### 2.9.0

- Fix for PS1.6 cache creation 

### 2.8.9

- Added "product_shop", "product_attribute_shop" as manual assignable fields

### 2.8.8

- Added support for multilanguage in cache
- Added support for "region_code" in Order submissions

### 2.8.7

- Added cronjob for precreating products JSON data

### 2.8.6

- Further improved caching, introducing new database table for specific cache scenarios and cronjob possibility to manually update cache

### 2.8.5

- Advanced caching mechanism for feed creation to avoid high load when checking category-trees with native PrestaShop methods

### 2.8.4

- Possibility to set an Employee (backend user) for order creation. This could prevent automatic stock update errors in some cases.

### 2.8.3

- Change in main class to avoid errors in PHP7.0 integrations (backward compatibility)

### 2.8.2

- Fix "order_paid_real" value for specific configurations

### 2.8.1

- Handling of "middle_name" dataset.
- Improved tax calculation

### 2.8.0

- Call of specific hook "channableFeed" at the end of each processed feed item. Merchants now can implement own modifications of each item in individual modules. 

### 2.7.9

- Included ProductSupplier object / table to fetch supplier references for product feed

### 2.7.8

- Improvement handling manual carrier tax in order creation

### 2.7.7

- Order view extended for PS > 1.7.7.x, new option in backend configuration to view channable order notes in PrestaShop order overview grid

### 2.7.6

- integration 3rd party plugin "wkproductcustomfield" in product feed

### 2.7.5

- Stock update config interface

### 2.7.4

- Improvement order creation

### 2.7.3

- Bugfix ecotax

### 2.7.2

- Import shipping rates: tax rate import now possible

### 2.7.1

- Cronjob update

### 2.7.0

- Extended logging class

### 2.6.9

- Order weight added

### 2.6.8

- implementation of optional validateOrder Hook
- improved group settings

### 2.6.7

- Customer group to be set via channel/marketplace

### 2.6.6

- Company in shipping address

### 2.6.5

- Fix admin config

### 2.6.4

- Compatibility PS 1.7.7.0

### 2.6.3

- Pricing workarround for multishop

### 2.6.2

- Workarround stock updates for older PS versions to fix PS internal bug

### 2.6.1

- Update for invoice creation
- Stock management improvement

### 2.6.0

- Fix payment transaction ID creation 
- added Currency detection
- added billing company

