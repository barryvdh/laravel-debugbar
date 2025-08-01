# Changelog

## v3.16.0 - 2025-07-21

### What's Changed

* Make all scalar config values configurable through environment variables by @wimski in https://github.com/barryvdh/laravel-debugbar/pull/1784
* Check if file exists on FilesystemStorage by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1790
* Bump php-debugbar by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1791
* Fix counter tests by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1792
* `$group` arg support on TimelineCollectors methods by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1789
* Collect other eloquent model events by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1781
* Add new cache events on CacheCollector by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1773
* Exclude events on EventCollector by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1786
* Use `addWarning` on warnings, silenced errors, notices by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1767
* Do not rely on DB::connection() to get information in query collector by @cweiske in https://github.com/barryvdh/laravel-debugbar/pull/1779
* Trace file for Gate checks(GateCollector) by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1770
* Fix support for PDOExceptions by @LukeTowers in https://github.com/barryvdh/laravel-debugbar/pull/1752
* Time measure on cache events by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1794
* fix debugbar for Lumen usage by @flibidi67 in https://github.com/barryvdh/laravel-debugbar/pull/1796
* Custom path for Inertia views by @joaopms in https://github.com/barryvdh/laravel-debugbar/pull/1797
* Better contrast in dark theme titles. by @angeljqv in https://github.com/barryvdh/laravel-debugbar/pull/1798

### New Contributors

* @wimski made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1784
* @cweiske made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1779
* @flibidi67 made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1796
* @joaopms made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1797

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.15.4...v3.16.0

## v3.15.4 - 2025-04-16

### What's Changed

* Remove html `<a/>` tag from route on clockwork by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1777
* Fix default for capturing dd/dump by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1783

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.15.3...v3.15.4

## v3.15.3 - 2025-04-08

### What's Changed

* Add condition for implemented query grammar by @rikwillems in https://github.com/barryvdh/laravel-debugbar/pull/1757
* Collect dumps on message collector by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1759
* Fix `capture_dumps` option on laravel `dd();` by @parallels999 in https://github.com/barryvdh/laravel-debugbar/pull/1762
* Preserve laravel error handler by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1760
* Fix `Trying to access array offset on false on LogsCollector.php` by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1763
* Update css theme for views widget by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1768
* Fix laravel-debugbar.css on query widget by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1765
* Use htmlvardumper if available on CacheCollector by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1766
* Update QueryCollector.php fix issue #1775 by @Mathias-DS in https://github.com/barryvdh/laravel-debugbar/pull/1776
* Better grouping the events count by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1774

### New Contributors

* @rikwillems made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1757
* @Mathias-DS made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1776

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.15.2...v3.15.3

## v3.15.2 - 2025-02-25

### What's Changed

* Fix empty tabs on clockwork by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1750
* fix: Ignore info query statements in Clockwork converter by @boserup in https://github.com/barryvdh/laravel-debugbar/pull/1749
* Check if request controller is string by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1751

### New Contributors

* @boserup made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1749

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.15.1...v3.15.2

## v3.15.1 - 2025-02-24

### What's Changed

* Hide more empty tabs  by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1742
* Always show application by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1745
* Add conflict with old debugbar by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1746

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.15.0...v3.15.1

## v3.15.0 - 2025-02-21

### What's Changed

* Add middleware to web to save session by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1710
* Check web middleware by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1712
* Add special `dev` to composer keywords by @jnoordsij in https://github.com/barryvdh/laravel-debugbar/pull/1713
* Removed extra sentence by @cheack in https://github.com/barryvdh/laravel-debugbar/pull/1714
* Hide empty tabs by default by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1711
* Combine route info with Request by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1720
* fix: The log is not processed correctly when it consists of multiple lines. by @uniho in https://github.com/barryvdh/laravel-debugbar/pull/1721
* [WIP] Use php-debugbar dark theme, move to variables by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1717
* Remove openhandler overrides by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1723
* Drop Lumen And Laravel 9 by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1725
* Use tooltip for Laravel collector by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1724
* Add more data to timeline by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1726
* Laravel version preview as repo branch name by @angeljqv in https://github.com/barryvdh/laravel-debugbar/pull/1727
* Laravel 12 support by @jonnott in https://github.com/barryvdh/laravel-debugbar/pull/1730
* Preview action_name on request tooltip by @angeljqv in https://github.com/barryvdh/laravel-debugbar/pull/1728
* Map tooltips by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1732
* Add back L9 by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1734
* Fix tooltip url by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1735
* Show request status as badge by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1736
* Fix request badge by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1737
* Use Laravel ULID for key by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1738
* defer datasets by config option by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1739
* Reorder request tab by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1740
* Defer config by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1741

### New Contributors

* @cheack made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1714
* @angeljqv made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1727
* @jonnott made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1730

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.14.10...v3.15.0

## v3.14.10 - 2024-12-23

### What's Changed

* Fix Debugbar spelling inconsistencies by @ralphjsmit in https://github.com/barryvdh/laravel-debugbar/pull/1626
* Fix Visual Explain confirm message by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1709

### New Contributors

* @ralphjsmit made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1626

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.14.9...v3.14.10

## v3.14.9 - 2024-11-25

### What's Changed

* Fix custom prototype array by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1706

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.14.8...v3.14.9

## v3.14.8 - 2024-11-25

### What's Changed

* Add fix + failing test for custom array prototype by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1705

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.14.7...v3.14.8

## v3.14.7 - 2024-11-14

### What's Changed

* Make better use of query tab space by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1694
* Do not open query details on text selecting by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1693
* Add (initial) support for PHP 8.4 by @jnoordsij in https://github.com/barryvdh/laravel-debugbar/pull/1631
* More warnings by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1696
* Fix sql-duplicate highlight by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1699
* ci: Use GitHub Actions V4 by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1700
* Fix "Uncaught TypeError: is not iterable" by @erikn69 in https://github.com/barryvdh/laravel-debugbar/pull/1701
* Fix Exception when QueryCollector softLimit exceeded by @johnkary in https://github.com/barryvdh/laravel-debugbar/pull/1702
* Test soft/hard limit queries by @barryvdh in https://github.com/barryvdh/laravel-debugbar/pull/1703

### New Contributors

* @johnkary made their first contribution in https://github.com/barryvdh/laravel-debugbar/pull/1702

**Full Changelog**: https://github.com/barryvdh/laravel-debugbar/compare/v3.14.6...v3.14.7
