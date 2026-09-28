# V8Js

PHP bindings for the [V8 JavaScript engine](https://v8.dev/), via [phpv8/v8js](https://github.com/phpv8/v8js).

## Build notes

This layer compiles V8 from source during the Docker build. Expect **long CI runtimes** (often 1–3 hours per PHP version) and a **large layer** (V8 shared libraries and ICU data).

Pinned versions:

- V8: `12.0.267.36`
- v8js: commit `8a39efa3cf3b275e402ddf3c4f6b611a5f69a499`

The extension is built with `V8_COMPRESS_POINTERS` and `V8_ENABLE_SANDBOX` to match the V8 build configuration.

## Lambda limits

Check the unzipped layer size against the [Lambda deployment package limit](https://docs.aws.amazon.com/lambda/latest/dg/gettingstarted-limits.html) (250 MB unzipped for direct uploads). This layer is significantly larger than typical PECL extensions.
