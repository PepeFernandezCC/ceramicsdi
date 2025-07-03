## Trusted Shops Official Module for PrestaShop

[![Coding Standart](https://github.com/trustedshops/connect-prestashop-app/actions/workflows/php.yml/badge.svg?branch=develop)](https://github.com/trustedshops/connect-prestashop-app/actions/workflows/php.yml)

[![Unit tests](https://github.com/trustedshops/connect-prestashop-app/actions/workflows/phpunit.yml/badge.svg?branch=develop)](https://github.com/trustedshops/connect-prestashop-app/actions/workflows/phpunit.yml)

## About

More than 30,000 online shops throughout Europe use the Trusted Shops Trustmark, Buyer Protection and authentic reviews 
for more traffic, higher sales and better conversion rates.

## Installation

To install module on PrestaShop, download zip package form [Product page on PrestaShop Addons](https://addons.prestashop.com/en/customer-reviews/25815-trusted-shops-reviews-toolkit.html#overview).

This module contain composer.json file. If you clone or download the module from github
repository, run the ```composer install``` from the root module folder.

See the [composer documentation](https://getcomposer.org/doc/) to learn more about the composer.json file.

## Compiling assets
**For development**

We use _Webpack_ to compile our javascript and scss files.  
In order to compile those files, you must :
1. have _Node 10+_ installed locally
2. run `npm install` in the root folder to install dependencies
3. then run `npm run watch` to compile assets and watch for file changes

**For production**

Run `npm run build` to compile for production.  
Files are minified, `console.log` and comments dropped.

## Cs fixer

`php vendor/bin/php-cs-fixer fix --no-interaction --dry-run --diff` to show code lines to be fixed.

`php vendor/bin/php-cs-fixer fix` to fix automatically all files.

## Properties

In the root of module directory you can find a file **.properties.json.master** for advanced configuration.  
If you want to change settings inside this file, make a copy of it and save it as **.properties.json**. With this you make sure a plugin update does not override your custom settings.  
**As this file is being read only during the installation, any changes to this file will need a reset of the module to take effect.**  
**Note:** Please respect the format of json structure.

These are the default settings in the file and they are **mandatory**: 
```json
{
  "skuType": "sku",
  "gtinType": "ean13",
  "order_status_reviews": {
    "all": {
      "trigger_order_status_id": 4,
      "reviews_nb_days": 3
    },
  }
}
```

### Description line by line:
- *skuType* : 
Used to define the field from the database in order to identify a product. The default value is **‘sku’** (taken from the Prestashop database’s field reference).  
Alternative value often used is: **‘id’** (Database field reference for ID)
  
- *gtinType* :
Used to define a database field in order to obtain the GTIN of a product. The default field used in Prestashop is: **‘ean13’**

- *order_status_reviews* :
This object determines the behavior of the review invite e-mail sending, if the option *'Use order status ”Shipped” to send Review Invites'* is selected in the plugin configuration.  
It is being used to trigger the review invite e-mail according to the shipping methods and order status.

- *“all”* :
  **This object is mandatory.**  
  The settings inside are applied to **all** shipping methods.
  
- *“1”, “2”, ... etc.* :
In addition to “all” you can add further elements with the reference ID of the respective shipping methods(also called carriers). 
The ID(s) can be found inside the database of the Prestashop instance. The table to look for is **ps_carrier** and the field containing the ID is **id_carrier**.  
**Note:** the *id_carrier field* changes whenever there is a change within the carrier settings. The id in this .properties.json file must then be adjusted accordingly.

- *trigger_order_status_id* :
Here should be input the **ID** of the order status you would like to use to trigger the sending of a review invite e-mail.  
The default order status used is *“Shipped”* and its ID is 4.  
The order status ID can be found from the Prestashop backend: *Configure -> Shop Parameters -> Order settings -> Statuses*.

- *reviews_nb_days* :
This parameter sets the invite sending delay as number of days. It can be set for each shipping method separately or it could be the same for all of them. 
In case you have activated the *"trigger date"* in your eTrusted Control Center, the review invite e-mail will be sent with the delay from here + the sending delay defined for the *order_shipped* event in the eTrusted control center.  
See this article for details regarding the trigger date in eTrusted: [https://help.etrusted.com/hc/en-gb/articles/360055245931-How-can-I-benefit-from-the-activation-date-of-sending-delay-](https://help.etrusted.com/hc/en-gb/articles/360055245931-How-can-I-benefit-from-the-activation-date-of-sending-delay-)  
**Example:** If you set reviews_nb_days to 3 days and in eTrusted’s control center you have a sending delay of 3 days, the review invite e-mail will be sent 6 days after the order status has been changed to *“Shipped”* in Prestashop’s backend.

### Example: Custom settings for a carrier with the ID 1:
```json
"order_status_reviews": {
  "all": {
    "trigger_order_status_id": 4,
    "reviews_nb_days": 3
  },
  "1": {
    "trigger_order_status_id": 5,
    "reviews_nb_days": 1
  }
}
```

In this example the order status with ID 4 is *Shipped* and the one with ID 5 is *Delivered*.  
So, in this case for the carrier with ID equal to 1, the review invite e-mail will be sent 1 day after the order’s status has been changed to *“Shipped”*.  
For all other carriers, the review invite e-mail will be sent 3 days after the order status has been changed to *“Delivered”*
