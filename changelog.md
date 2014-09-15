# Changelog for Laravel Debugbar

## 1.7.7 (2014-09-15)

- Make it compatible with Laravel 5.0-dev
- Allow anonymous function as `enabled` setting (for IP checks etc)
- Escape query bindings, to prevent executing of scripts/html

## 1.7.6 (2014-09-12)

- Fix reflash bug
- Fix caching of debugbar assets

## 1.7.5 (2014-09-12)

- Reflash data for all debugbar requests

## 1.7.4 (2014-09-08)

- Rename assets routes to prevent Nginx conflicts

## 1.7.3 (2014-09-05)

- Add helper functions (debug(), add/start/stop_measure() and measure()
- Collect data on responses that are not redirect/ajax/html also.

## 1.7.2 (2014-09-04)

- Fix 4.0 compatibility (problem with Controller namespace)
- Give deprecation notice instead of publishing assets.

## 1.7.1 (2014-09-03)

- Deprecated `debugbar:publish` command in favor of AssetController
- Fixed issue with detecting absolute paths in Windows

## 1.7.0 (2014-09-03)

- Use AssetController instead of publishing assets to the public folder.
- Inline fonts + images to base64 Data-URI
- Use PSR-4 file structure

## 1.6.8 (2014-08-27)

- Change OpenHandler layout
- Add backtrace option for query origin

## 1.6.7 (2014-08-09)

- Add Twig extensions for better integration with rcrowe/TwigBridge

## 1.6.6 (2014-07-08)

- Check if Requests wantsJSON instead of only isXmlHttpRequest
- Make sure closure for timing is run, even when disabled 

## 1.6.5 (2014-06-24)

- Add Laravel style

## 1.6.4 (2014-06-15)

- Work on non-UTF-8 handling