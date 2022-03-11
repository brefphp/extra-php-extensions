# Change log

## 0.11.26

### Added

- Updated Blackfire to version 1.75.0.

## 0.11.25

### Added

- Updated Blackfire to version 1.74.1.

## 0.11.24

### Added

- Updated Blackfire to version 1.74.0.

## 0.11.23

### Added

- Updated Blackfire to version 1.73.0.
- Imagick support for PHP 8.1
- Blackfire support for PHP 8.1
- AMQP support for PHP 8.1
- DS support for PHP 8.1

## 0.11.22

### Added

- Kafka extension updated to 1.8.2
- Kafka support for PHP 8.1
- MongoDB support for PHP 8.1
- Tidy support for PHP 8.1
- Yaml support for PHP 8.1
- Serverless Framework v3 support

## 0.11.21

### Added

- Updated Blackfire to version 1.71.0.

## 0.11.20

### Added

- Updated Blackfire to version 1.70.0.
- Add lcms2 library for icc profile manipulation for imagick extension
- sqlsrv support for PHP 8.1
- Redis with Igbinary Serializer support for PHP 8.1

## 0.11.19

### Added

- Support for Tidy extension
- Support for SSH2 extension
- Support for Redis with Igbinary Serializer
- Updated Blackfire to version 1.69.0.
- Mailparse support for PHP 8.1

### Fixed

- Update all Dockerfile to compile with latest version of bref

## 0.11.18

### Added

- Updated Blackfire to version 1.68.0.

## 0.11.17

### Added

- New version of Oracle extension.

## 0.11.16

### Added

- Updated Blackfire to version 1.66.0.

## 0.11.15

### Changed

- Using bref/build-php-XX:1.2.13 as base image

### Added

- Support for Calendar on PHP 8.1.
- Support for GD on PHP 8.1.
- Support for GMP on PHP 8.1.
- Support for gRPC on PHP 8.1.
- Support for Igbinary on PHP 8.1.
- Support for Imap on PHP 8.1.
- Support for LDAP on PHP 8.1.
- Support for Memcached on PHP 8.1.
- Support for msgpack on PHP 8.1.
- Support for ODBC Snowflake on PHP 8.1.
- Support for pcov on PHP 8.1.
- Support for pgsql on PHP 8.1.
- Support for Redis on PHP 8.1.
- Support for UUID on PHP 8.1.
- Support for Xdebug (master) on PHP 8.1.

## 0.11.14

### Added

- Updated Blackfire to version 1.65.0.

## 0.11.13

### Added

- Updated Kafka (rdkafka) extension.

## 0.11.12

### Added

- Updated Oracle (oci8) extension.

## 0.11.11

### Added

- Updated Blackfire to version 1.64.0.

## 0.11.10

### Added

- Updated Blackfire to version 1.63.0.

## 0.11.9

### Added

- Updated Blackfire to version 1.62.0.

## 0.11.8

### Added

- Updated Blackfire to version 1.61.0.

## 0.11.7

### Added

- Updated Blackfire to version 1.60.0.

## 0.11.6

### Added

- Updated Blackfire to version 1.59.2.
- Fixed broken CI job for GD layer.

## 0.11.5

### Added

- Updated Blackfire to version 1.59.1.
- Added SQL Server support on PHP8.

## 0.11.4

### Added

- Added layer for Symfony Runtime
- Added gRPC support on PHP8.

## 0.11.3

### Added

- Updated Blackfire to version 1.58.0.

## 0.11.2

### Added

- Added NewRelic support on PHP8.

## 0.11.1

### Added

- Updated Blackfire to version 1.57.0.

## 0.11.0

### Changed

Added support for Serverless new variable system. This means we had to add a small
BC break to update the names of the layers.

```diff
- ${bref:extra.gd-php-74
+ ${bref-extra:gd-php-74}
```

## 0.10.10

### Added

- Updated Blackfire to version 1.56.1.

## 0.10.9

### Added

- Updated Blackfire to version 1.56.0.

## 0.10.8

### Added

- Updated Blackfire to version 1.55.0.

## 0.10.7

### Added

- Added Calendar extension.
- Added AMQP support on PHP8.

## 0.10.6

### Added

- Updated Blackfire to version 1.54.0.

## 0.10.5

### Added

- Added SPX extension.
- Added Blackfire support on PHP8.

## 0.10.4

### Added

- Updated Blackfire to version 1.53.0.

## 0.10.3

### Added

- Updated Blackfire to version 1.52.0.

## 0.10.2

## Fixed

- Updated extension version for Imagick
- Included the latest version of ghostscript library in Imagick
- Updated extension version for sqlsrv

## 0.10.1

### Added

- Updated Blackfire to version 1.51.0.

## 0.10.0

### Changed

- Using bref/build-php-XX:1.1.4 as base image

## 0.9.7

### Added

- Updated Blackfire to version 1.50.0.

## 0.9.6

### Added

- Updated Blackfire to version 1.49.1.
- Added msgpack for PHP8

### Fixed

- Bug in imagick when writing texts

## 0.9.5

### Added

- Updated Blackfire to version 1.49.0.

## 0.9.4

- Added NewRelic extension
- Added Imap extension

## 0.9.3

### Added

- Updated Blackfire to version 1.48.1.

## 0.9.2

### Added

- Updated Blackfire to version 1.48.0.

## 0.9.1

### Added

- Updated Blackfire to version 1.47.0.

## 0.9.0

### Added

- Support for Imagick on PHP 8.

### Changed

- Separated Memcache and Memcached layers. Memcache users should use the new
`${bref:extra.memcache-php-74}` layer. Memcached users dont need to include the
`memcached.so` file anymore, it is done automatically.

## 0.8.1

No changes to the layers compared to 0.8.0. This version and future versions will
add more tag on the docker images. It will tag images for major, minor and patch
versions.

## 0.8.0

This is just a super small BC break. If you are not using Microsoft SQLSRV layer
then you can upgrade with no worry.

### Changed

- You dont need to add `extension=/opt/bref-extra/sqlsrv.so` or `extension=/opt/bref-extra/pdo_sqlsrv.so` in your php.ini anymore.

### Fixed

- Issue another issue with SQLSRV extension.

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
