# Quality Recommendations

This package now has CI coverage for Laravel 9 through 13, plus runnable style and static analysis checks. The GitHub Actions quality job is advisory until the codebase has been cleaned to the desired Pint and PHPStan level.

## Current Checks

- `composer validate --strict` validates package metadata.
- `composer test` runs the PHPUnit 10+ test configuration.
- `composer test:phpunit9` runs the PHPUnit 9 configuration used by Laravel 9.
- `composer lint` checks Laravel Pint formatting.
- `composer analyse` runs Larastan/PHPStan against `src`.

## Recommended Next Improvements

- Raise `phpstan.neon` from level 0 gradually after the Laravel 9-13 matrix is green.
- Add package-specific exceptions for `mix_cdn()` instead of throwing generic `Exception`.
- Validate `asset-cdn.files` configuration shape before reading nested keys.
- Reduce remote reads in `asset-cdn:sync`; it currently downloads matching-size CDN files to compare hashes.
- Add tests for `ignoreVCS` and `ignoreDotFiles`.
- Add command failure tests for upload and delete errors.
- Promote style and static analysis to required branch protection checks once the codebase is clean at the desired level.
