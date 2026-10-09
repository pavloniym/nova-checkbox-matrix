# Changelog

All notable changes to this project are documented in this file.
The project follows [Semantic Versioning](https://semver.org).

## [0.1.1] - 2026-10-09

### Fixed

- Rows keyed `constructor`, `toString`, `hasOwnProperty`, `__proto__` and other `Object.prototype` member names
  broke the form (TypeError) or could replace the selection's prototype. Selections are now prototype-less and
  every lookup is an own-property check.
- `composer.json` now requires `laravel/nova: ^5.0`, so the package can no longer be installed next to Nova 4
  or without Nova.
- The first README example missed the `MatrixValue` import.

### Added

- JS tests for the selection helpers (`node --test`), run in CI.

## [0.1.0] - 2026-10-08

Initial release.
