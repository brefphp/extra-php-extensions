# Change log

## 0.7.3

### Fixed

- Issue with SQLSRV extension.

## 0.7.2

### Added

- Updated Blackfire to version 1.46.4.

### Changed

- Removed PHP 7.2. They cannot be used with Bref 1.0

## 0.7.1

### Fixed

- Added support for PNG and JPG for Imagick.
- Updated Imagick to version 7

## 0.7.0

Version 0.6.x was a mess for multiple reasons. Let's try to start fresh with 0.7.0.
It includes a tiny BC break (ie, you dont need to add `extensions=/opt/bref-extra/*.so`).

### Added

- Testing for each layer to make sure they do not cause issues in production.
- Updated mongodb to version 1.9.0 with php80 compatibility.

### Changed

- Most layers will automatically include the extension file.

### Fixed

- Compile issues with GD and Snowflake layers.

## 0.6.4

### Changed

- Using a new config.json to control which php versions each extension is built.
- Make sure docker is using `1.0.0` tag instead of `1.0.0-beta1`.

## 0.6.3

### Added

- Updated Blackfire to version 1.46.0.

## 0.6.2

### Fixed

- Make sure docker is using `1.0.0-beta1` tag instead of `latest`.

## 0.6.1

### Added

- Updated Blackfire to version 1.45.0.

## 0.6.0

### Changed

- Removed ZTS from all extensions.

## 0.5.3

### Added

- Updated Blackfire to version 1.44.0.

## 0.5.2

### Added

- Updated Blackfire to version 1.43.0.
- SQLSRV extension

## 0.5.1

### Added

- Updated Blackfire to version 1.42.0.

## 0.5.0

### Added

- Scrypt extension
- GD extension
- Msgpack extension
- LDAP extension

### Changed

All images is built `FROM scratch` now.

## 0.4.2

### Added

- Redis extension

## 0.4.1

### Added

- Updated Blackfire to version 1.39.1.

## 0.4.0

### Added

- Updated Blackfire to version 1.39.0.
- Added MongoDB

### Changed

- Builds can now be automated.

## 0.3.3

- Support for UUID
- Support for Yaml

## 0.3.2

### Added

- Support for gRPC

## 0.3.1

### Added

- Support for Cassandra
- Support for Data Structures
- Support for Igbinary
- Support for ImageMagick
- Support for ODBC Snowflake

## 0.3.0

### Added

- Support for Mailparse
- Support for pcov

### Changed

- Publishing docker images for each layer
- Use AsyncAws internally to maintain images

## 0.2.1

### Added

- Support for pgsql

## 0.2.0

### Added

- Support for Memcahced
- Support for Memcahce
- Support for GMP

## 0.1.0

First release
