# Project Version (EXT:project_version)

[![CI](https://github.com/KamiYang/project_version/actions/workflows/ci.yml/badge.svg)](https://github.com/KamiYang/project_version/actions/workflows/ci.yml)

## What is project version?

Project version is a TYPO3 extension that adds an entry to the TYPO3 "System Information" dropdown in the
backend toolbar. The entry shows the version of *your project* (not TYPO3's), taken either from a plain
`VERSION` file, from the local git checkout or from a statically configured value.

## Requirements

| project_version | TYPO3            | PHP     |
| --------------- | ---------------- | ------- |
| 2.x             | 13.4, 14, 15     | >= 8.2 (TYPO3 15 requires PHP 8.5) |
| 1.x             | 8.7, 9.5         | >= 7.0  |

TYPO3 v15 support is based on the current development branch (`15.0.x-dev`) and will be re-validated once
15.0 is released.

## Installation

```bash
composer require kamiyang/ext-projectversion
```

Or, in a classic (non-composer) installation, install "Project Version" from the TYPO3 Extension Repository (TER).

No further setup is required. The extension registers a PSR-14 event listener for
`SystemInformationToolbarCollectorEvent` and an icon; there is no TypoScript and no database table.

## Usage

### VERSION file (default)

Create a file called `VERSION` (case sensitive) containing the project version:

```bash
echo "1.0.0-rc.3" > VERSION
```

Relative paths are looked up in this order:

1. the public web directory (`public/` in composer mode, the document root otherwise)
2. the project directory (the composer root in composer mode)

So both `/var/www/html/public/VERSION` and `/var/www/html/VERSION` work out of the box.

### Custom path

The file name and location can be changed in the extension settings
(*Admin Tools > Settings > Extension Configuration > project_version*, setting `versionFilePath`).

| Configured path                          | Resolved to                                                        |
| ---------------------------------------- | ------------------------------------------------------------------ |
| *(empty, default)*                       | `<public>/VERSION`, then `<project>/VERSION`                       |
| `MyVersion`                              | `<public>/MyVersion`, then `<project>/MyVersion`                   |
| `config/`                                | `<public>/config/VERSION`, then `<project>/config/VERSION`         |
| `EXT:my_sitepackage/VERSION`             | the file inside the given extension                                |
| `/srv/deploy/current/VERSION`            | absolute paths are used as given                                   |

A path ending with `/` is treated as a directory and `VERSION` is appended.

### Git

Set `mode` to *GIT* (or *GIT (VERSION file as fallback)*) in the extension settings. The `git` binary must
be available to the web server user and PHP's `exec()` must not be disabled. If git cannot be used, the
fallback mode reads the VERSION file instead.

`gitFormat` controls what is displayed:

| Format                        | Example              |
| ----------------------------- | -------------------- |
| Revision                      | `a1b2c3d`            |
| \[revision\] branch (default) | `[a1b2c3d] main`     |
| \[revision\] tag              | `[a1b2c3d] v1.4.0`   |
| Branch                        | `main`               |
| Tag                           | `v1.4.0`             |

### Static version

Set `mode` to *Static Version* and put the value into `staticVersion`. Useful when the version is injected
at deploy time via `config/system/additional.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['project_version']['mode'] = '3';
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['project_version']['staticVersion'] = getenv('APP_VERSION') ?: 'dev';
```

### Using the version in your own code

`\KamiYang\ProjectVersion\Service\ProjectVersionService` is a public service and can be injected:

```php
public function __construct(private readonly ProjectVersionService $projectVersionService) {}

$version = $this->projectVersionService->getProjectVersion()->getVersion();
```

`getVersion()` returns a `LLL:` label when no version could be resolved; check `isUnknown()` first.

## Upgrading from 1.x

Version 2.0 drops support for TYPO3 8.7 and 9.5 and requires PHP 8.2. Extension settings keep their names and
values, so no reconfiguration is needed. Internal classes were renamed:

| 1.x                                                          | 2.x                                                              |
| ------------------------------------------------------------ | ---------------------------------------------------------------- |
| `Backend\ToolbarItems\ProjectVersionSlot` (signal/slot)      | `Backend\EventListener\SystemInformationListener` (PSR-14)       |
| `Enumeration\ProjectVersionModeEnumeration`                  | `Enumeration\ProjectVersionMode` (native enum)                   |
| `Enumeration\GitCommandEnumeration`                          | `Enumeration\GitCommand`, `Enumeration\GitFormat` (native enums) |
| `Configuration\ExtensionConfiguration` (static)              | `Configuration\ExtensionConfiguration` (injectable service)      |
| `Facade\LocalizationUtilityFacade`, `Facade\SystemEnvironmentBuilderFacade` | removed                                           |

## Development

```bash
composer install
composer ci          # lint, php-cs-fixer, phpstan, phpunit
composer fix:cs      # apply coding standard
```

A docker based environment for TYPO3 13, 14 and 15 is described in `.Build/README.md` after the first
`composer install` (the directory is git-ignored; copy the compose files from this repository's history or
create your own).

## License

GPL-3.0-or-later, see [LICENSE](LICENSE).
