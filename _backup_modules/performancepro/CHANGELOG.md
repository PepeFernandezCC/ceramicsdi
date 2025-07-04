# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.6.0] - 2023-07-29

### Added

- Option to lazy load audio

## [2.5.2] - 2023-07-14

### Improvements

- Let HTTP cache depends on device size
- Don't override the "loading" attribute with "lazy" if "eager" is explicit set.
- Updated dependencies

## [2.5.1] - 2023-02-07

### Fixes

- Fix HTML redering problem

## [2.5.0] - 2022-11-25

### Fixes

- Fix page cache not getting set
- Fix rare issue where cookie is not set on the first load when the cache was set

### Improvements

- Update dependencies
- Minify inline application/ld+json
- PHP 8.1 improvements

### Added

- German translation

## [2.4.2] - 2022-10-29

### Fixes

- Fix encoding of characters

## [2.4.1] - 2022-10-19

### Fixes

- Fix encoding of characters

### Improvements

- PrestaShop 8.0 compatible check
- Added support for srcset for WebP

## [2.4.0] - 2022-07-28

### Fixes

- Compatible fix with blog module
- PHP 8.2 compatible fix

### Improvements

- General improvements

## [2.3.4] - 2022-06-01

### Fixes

- Minor bug fix

### Improvements

- Improvements for 3rd party themes

## [2.3.3] - 2022-04-25

### Fixes

- Fix paypal issue

### Improvements

- Minor improvements

## [2.3.2] - 2022-04-22

### Fixes

- Fix page cache currency issue

## [2.3.1] - 2022-04-01

### Fixes

- Fix WebP generation of images with params in the URL
- Fix resize images display

## [2.3.0] - 2022-03-14

### Improvements

- Improved defragmentation of tables
- Minor improvements

## [2.2.1] - 2022-03-10

### Improvements

- Minor improvements
- Improved 3rd part theme compatible

## [2.2.0] - 2022-03-07

### Added

- Added translations

### Improvements

- Improved cache warmer

## [2.1.0] - 2022-02-27

### Added

- Added cache warmer
- Added Italian language
- Added Slovenian language

### Improvements

- General improvements

## [2.0.0] - 2022-02-22

### Fixes

- Fix problem with preload and prefetch multiple URL's

### Added

- Added feature to disable DOM parser on the checkout page
- Added display of execution time on a cronjob
- Added support for image extensions in uppercase
- Added limit to the error log size

### Improvements

- Improve wording
- Refactored exception handling for ajax call
- Better exception logging
- If PHP ini value is empty fallback on default value
- Improved page cache

## [1.3.1] - 2022-02-15

### Fixes

- Work around PrestaShop bug in PrestaShop 1.7.5
- Add 'data-src' to the list of attributes that get converted to WebP

## [1.3.0] - 2022-02-14

### Fixes

- Fixed case where images not found can cause troubles

### Improvements

- Refactored WebP generation
- Performance improvements
- Improved wording

### Added

- Added option to log errors

## [1.2.3] - 2022-02-08

### Fixes

- Added useragent when using copy()

## [1.2.2] - 2022-02-08

### Fixes

- Fix case where image name has params in it

### Improvement

- Improved compatible with cookie modules

## [1.2.1] - 2022-02-08

### Fixes

- Fix input validation backoffice

### Improvement

- Improved cache

## [1.1.0] - 2022-02-07

### Fixes

- Clear HTTP cache when PS cache or media cache is cleared

### Improvement

- Improved error handle

### Added

- Added option to set different compression for JPEG and PNG

## [1.0.3] - 2022-02-06

### Improvement

- Minor improvements

## [1.0.2] - 2022-02-06

### Fixes

- Fix case where the image URL includes special characters

### Improvement

- Improved the WebP generation

## [1.0.1] - 2022-01-06

### Fixes

- Fix preload/preconnect template bug
- Fix path of new WebP for relatives URL's
- Fix case where DB table identifier is more than 64 characters

### Improvement

- Improved the error handle of ajax calls

## [1.0.0] - 2022-02-01

### Improvements

- Init
