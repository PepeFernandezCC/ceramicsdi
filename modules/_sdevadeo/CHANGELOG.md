# CHANGELOG

## Version 1.1.7 [2023-05-31]

Fix :

* Price tax request

## Version 1.1.6 [2023-05-24]

Fix :

* Tables missing on MariaDB contexts

## Version 1.1.5 [2023-05-16]

Fix :

* Offer flow taxes
* Table installation on MariaDB contexts

## Version 1.1.4 [2023-04-24]

Fix :

* DNI integration at order import

* Shop information data recovery

## Version 1.1.3 [2023-03-21]

Fix :

* Category mapping verification

* Supplier verification

* Manufacturer verification

## Version 1.1.2 [2023-03-21]

Fix :

* Wizard mapping URL.

* Default category label used on product flow.

* Adjusted configuration values check

## Version 1.1.1 [2023-03-16]

Fix :

* Add PT platform and tax.

* Add DNI number to orders imported.

## v1.1.0 :

Fix :

* Remove API categories handle, mapping and integration.

* Remove models and controllers that are not used anymore.

* Adapt products and offer flows

## v1.0.1 :

* Fix the attribute import that skipped entries that wasn't already not imported

* Fix value mapping where attributes without mapping tried to display non-existent values

## v1.0.0 :

- Manufacturer and supplier filter also apply to offer flow

## v0.0.27 :

- Description is put in product flow as html content instead of raw text and prohibited tags are excluded
- Fix issue with tracking information that used id_carrier instead of reference causing error when carrier has been updated
- Implement logs when API return error

## v0.0.26 :

- Fix category and attributes import by keep proceeding if some are having issues

## v0.0.25 :

- Fix product flow by perform action on initiated action without check product state

## v0.0.24 :

- Fix relay integration on order import
- Fix processes that import attributes if categories are not imported

## v0.0.23 :

- Fix variable that prevent values display

## v0.0.22 :

- Display category which is affected by the attribute
- Add variation labels to product name


## v0.0.21 :
- Fix limit display of report to 10 in order to avoid Too Much Request to API
- Fix namespace issue happening on call of productFlowController

## v0.0.20 :
- Force negative stock to zero
- Check if at least one language is available before import label translation

## v0.0.19 :
- Fix offer flow cron task

## v0.0.18 :
- Remove security on offer flow at end of CRON task

## v0.0.17 :
- Add adeo documentation

## v0.0.16 :
- Handle both state "SEND" and "COMPLETE" for product report