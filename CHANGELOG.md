# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [1.2.0] - 2021-12-29

### Changed

- Create a seekable stream when reading. Allows http range reques… - [#522](https://github.com/owncloud/files_primary_s3/issues/522)
- Update info.xml - [#495](https://github.com/owncloud/files_primary_s3/issues/495)

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

[1.2.0]: https://github.com/owncloud/files_primary_s3/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/owncloud/files_primary_s3/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/owncloud/files_primary_s3/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/owncloud/files_primary_s3/compare/v1.0.4...v1.1.0
[1.0.4]: https://github.com/owncloud/files_primary_s3/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/owncloud/files_primary_s3/compare/v1.0.2..v1.0.3
[1.0.2]: https://github.com/owncloud/files_primary_s3/compare/v1.0.0..v1.0.2
