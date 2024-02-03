# Change log

## 1.4.1

- Updated Blackfire to version 1.92.9.

## 1.4.0

- Updated Blackfire to version 1.92.8.
- Added support for PHP 8.3
- Added xmlrpc extension

## 1.3.3

- Updated Blackfire to version 1.92.2.

## 1.3.2

- Updated Blackfire to version 1.90.0.

## 1.3.1

- Update PDO sqlsrv to 5.11.1

## 1.3.0

- Added extension SNMP
- Added extension Excimer
- Update elastic apm to version 1.8.4
- Use openssl 1.1.1w in relay extension

## 1.2.6

### Added

- Updated Blackfire to version 1.89.0.

## 1.2.5

### Added

- Updated Blackfire to version 1.88.1.

## 1.2.4

### Added

- Updated Blackfire to version 1.88.0.

## 1.2.3

* Fix Laravel support
* Upgrade Imagick

## 1.2.2

* Update layer versions (previous release did not include them).

## 1.2.1

* Specify specific version, so upgrades are explicit and not cached by @GrahamCampbell in https://github.com/brefphp/extra-php-extensions/pull/446
* Fixed broken amqp extension by @GrahamCampbell in https://github.com/brefphp/extra-php-extensions/pull/445
* Upgrade to grpc 1.55.0 by @GrahamCampbell in https://github.com/brefphp/extra-php-extensions/pull/449
* Fix the relay extension by @mnapoli in https://github.com/brefphp/extra-php-extensions/pull/448
* Update Blackfire extension by @bref-bot in https://github.com/brefphp/extra-php-extensions/pull/450
* Update FUNDING.yml by @mnapoli in https://github.com/brefphp/extra-php-extensions/pull/451

## 1.2.0

* Added `datadog` extension by @wolflingorg in https://github.com/brefphp/extra-php-extensions/pull/442
* ImageMagick upgrades by @GrahamCampbell in https://github.com/brefphp/extra-php-extensions/pull/434
* Upgrade NewRelic agent to v10.9.0.324 by @starred-gijs in https://github.com/brefphp/extra-php-extensions/pull/438
* OCI8 Update by @mixaster in https://github.com/brefphp/extra-php-extensions/pull/439
* Update Blackfire extension by @bref-bot in https://github.com/brefphp/extra-php-extensions/pull/444

## 1.1.2

### Added

- Updated Blackfire to version 1.87.1.

## 1.1.1

### Added

- Specify grpc version for reliable builds (#435)
- Rebuild MongoDB (#436)
- Updated Blackfire to version 1.86.8.

## 1.1.0

### Added

- Added extension for Swoole
- Added extension for bsdiff
- Upgrade Elastic APM to 1.8.1
- Added Blackfire support for 8.2

## 1.0.2

### Added

- Updated Blackfire to version 1.86.6.

## 1.0.1

### Added

- Updated Blackfire to version 1.86.5.
- Updated NewRelic to version 10.7.0.319.

## 1.0.0

## Breaking changes

- Just like Bref v2, support for PHP 7.3 and 7.4 was dropped. PHP 8.0 or greater is required.
- [Bref v2](https://bref.sh/docs/news/02-bref-2.0.html) is required. If you use Bref v1, use the `0.12` version of the Bref extra extensions.

## Internal changes

These internal changes will not impact most users, however we list them in case you have an advanced use case:

- The "bref-extra" PHP extensions are now installed in the official Bref directory for PHP extensions: `/opt/bref/extensions`. They were previously installed in `/opt/bref-extra`.

If you were copying the layers in your Docker images, make sure to copy the entire `/opt` folder instead of the `/opt/bref-extra` folder:

```dockerfile
FROM bref/php-82-fpm:2

# Don't do this:
COPY --from=bref/extra-imagick-php-82:1 /opt/bref-extra /opt/bref-extra
# Do this instead:
COPY --from=bref/extra-imagick-php-82:1 /opt /opt
```

## 0.12.5

### Added

- Updated Blackfire to version 1.86.4.

## 0.12.4

### Added

- Relay extension
- Tideways extension
- Update NewRelic

## 0.12.3

### Added

- Support for PHP 8.2 for Xlswriter
- Support for PHP 8.2 for oci8

### Updated

- Updated Blackfire to version 1.86.3.
- NewRelic agent to 10.4.0.316

## 0.12.2

### Added

- extensions compiled against PHP 8.2.0 stable release
- OpenSwoole extension
- Support for sqlsrv on PHP 8.2
- Added more AWS regions

### Changed

- Using bref/build-php-XX:1.7.14 as base image
- Updated NewRelic Agent to 10.3.0.315

## 0.12.1

### Added

- Updated Blackfire to version 1.85.0.

## 0.12.0

### Removed

- PHP 7 support

### Changed

- Using bref/build-php-XX:1.7.14 as base image

### Added

- Added Decimal extension
- Added gnupg extension
- Extensions support for PHP 8.2, all except following ones:
  - amqp
  - calendar
  - ds
  - gd
  - gmp
  - grpc
  - igbinary
  - imagick
  - imap
  - ldap
  - mailparse
  - maxminddb
  - memcached
  - mongodb
  - msgpack
  - odbc-snowflake
  - pcov
  - pgsql
  - rdkafka
  - redis
  - redis-igbinary
  - scoutapm
  - ssh2
  - symfony-runtime
  - tidy
  - uuid
  - xdebug
  - yaml

## 0.11.35

### Added

- Updated Blackfire to version 1.84.0.
- Added Xlswriter extension
- Added Elastic APM extension
- Updated dependencies for sqlsrv

## 0.11.34

### Added

- Updated Blackfire to version 1.81.0.

## 0.11.33

### Added

- Updated Blackfire to version 1.80.0.

## 0.11.32

### Added

- Updated Blackfire to version 1.79.0.

## 0.11.31

### Added

- Updated Blackfire to version 1.78.1.
- Updated ODBC snowflake driver to version 2.25.0
- Reduce size of ODBC layer

## 0.11.30

### Added

- Updated Blackfire to version 1.78.0.

## 0.11.29

### Added

- Updated Blackfire to version 1.77.0.
- Added ScoutAMP extension

## 0.11.28

### Added

- Added Maxminddb extension

## 0.11.27

### Added

- Updated Blackfire to version 1.76.0.

## 0.11.26

### Added

- Updated Blackfire to version 1.75.0.
- OCI8 support for PHP 8.1
- NewRelic support for PHP 8.1

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
