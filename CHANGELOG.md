# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [1.6.0]  - 2024-10-25

### Added

- [#679](https://github.com/owncloud/files_primary_s3/pull/679) - feat: BackBlaze B2 upload retry logic
- [#674](https://github.com/owncloud/files_primary_s3/pull/674) - feat: support seeking on LazyReadStream


## [1.5.0]  - 2023-07-27

### Changed

- [#664](https://github.com/owncloud/files_primary_s3/pull/664) - Always return an int from Symfony Command execute method
- Minimum core version 10.11, minimum php version 7.4


## [1.4.0]  - 2022-10-25

### Changed

- [#605](https://github.com/owncloud/files_primary_s3/pull/605) - Allow configurable concurrent uploads

### Fixed

- [#618](https://github.com/owncloud/files_primary_s3/pull/618) - Fix stream download release


## [1.3.0] - 2022-08-10

### Changed

- [#39387](https://github.com/owncloud/core/issues/39387) - Update guzzle major version to 7
- This version is compatible with both ownCloud 10.10 and ownCloud 10.11.0

## [1.2.0] - 2021-12-29

### Changed

- Create a seekable stream when reading. Allows http range requests - [#522](https://github.com/owncloud/files_primary_s3/issues/522)
- Update info.xml - [#495](https://github.com/owncloud/files_primary_s3/issues/495)

## [1.1.3] - 2021-11-09

### Fixed

- Prohibit enabling encryption when S3 Object Storage is configured [#487](https://github.com/owncloud/files_primary_s3/issues/487)


## [1.1.2] - 2020-04-22

### Fixed

- Bugfix/l10n - [#323](https://github.com/owncloud/files_primary_s3/issues/323)

### Changed

- Update PHP dependencies - [#316](https://github.com/owncloud/files_primary_s3/issues/316)

### Added

- Add phpdoc for Symfony Command execute - [#309](https://github.com/owncloud/files_primary_s3/issues/309)
- Add l10n to dist/appstore build - [#340](https://github.com/owncloud/files_primary_s3/issues/340)

## [1.1.1] - 2020-01-23

### Fixed

- Catch Multipart exception when uploading large files - [#304](https://github.com/owncloud/files_primary_s3/issues/304)

## [1.1.0] - 2019-12-23

### Fixed

- Format message thrown by AWS exception - [#287](https://github.com/owncloud/files_primary_s3/issues/287)

### Removed

- Remove support for PHP 7.0 - [#277](https://github.com/owncloud/files_primary_s3/issues/277)

## [1.0.4] - 2019-09-23

### Fixed

- Proper handling of objecstorage issues on object upload [#212](https://github.com/owncloud/files_primary_s3/pull/212)

### Changed

- Various library updates (`aws/aws-sdk-php`, `guzzlehttp/psr7`, `ralouphie/getallheaders` ) [#231](https://github.com/owncloud/files_primary_s3/pull/231)[#236](https://github.com/owncloud/files_primary_s3/pull/236) [#241](https://github.com/owncloud/files_primary_s3/pull/241)

## [1.0.3] - 2019-01-09

### Fixed

- Fix Makefile to use GNU tar to prevent extraction issues on some envs - [#173](https://github.com/owncloud/files_primary_s3/pull/174)

## [1.0.2] - 2018-12-07

### Changed

- Set max version to 10 because core is switching to Semver

## 1.0.0 - 2018-07-20

### Changed

- First marketplace release

[Unreleased]: https://github.com/owncloud/files_primary_s3/compare/v1.6.0...master
[1.6.0]: https://github.com/owncloud/files_primary_s3/compare/v1.5.0...v1.6.0
[1.5.0]: https://github.com/owncloud/files_primary_s3/compare/v1.4.0...v1.5.0
[1.4.0]: https://github.com/owncloud/files_primary_s3/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/owncloud/files_primary_s3/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/owncloud/files_primary_s3/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/owncloud/files_primary_s3/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/owncloud/files_primary_s3/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/owncloud/files_primary_s3/compare/v1.0.4...v1.1.0
[1.0.4]: https://github.com/owncloud/files_primary_s3/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/owncloud/files_primary_s3/compare/v1.0.2..v1.0.3
[1.0.2]: https://github.com/owncloud/files_primary_s3/compare/v1.0.0..v1.0.2
