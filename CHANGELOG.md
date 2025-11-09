# Changelog

All notable changes to this package are documented in this file.

## [1.0.0] — 2025-11-10

### Added

-   Base interface `ExceptionHandlerInterface`
-   `SmartExceptionHandler` class with support for:
-   Automatic HTTP status detection (404, 500, 401, etc.)
-   Debug mode (`debug.php`) and production mode (`generic.php`)
-   Fallback logic for template errors
-   Works without dependence on `View` (via custom `callable $viewRenderer`)
-   Base HTML templates:
-   `resources/views/errors/generic.php`
-   `resources/views/errors/debug.php`
-   Full unit test coverage (`PHPUnit 9.6–12.0`)
