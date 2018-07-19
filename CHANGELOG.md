Changelog
=========
## 1.0.1 - 2018-07-19
### Fixed
- fixed a bug when there is no `.env` fill it won't error and make you sad
- fixed merge issue when merging in items from local `.evo/config`

## 1.0.0 - 2018-07-18
### Added
- `params/print-dotenv` command now merges with the local .env if found. This will 
use the aws parameter over the local ones if there is a conflict.

### Fixed 
- aws sdk region exception when you specify an environment that doesn't exist

## 0.0.1-beta.10 - 2018-07-18
### Added
- adding delete parameter

### Changed
- updating apache docker-compose.yml

## 0.0.1-beta.6 - 2018-03-28
### Changed
- bumped yii dependency

## Unreleased
Initial release.
