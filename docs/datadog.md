# Configuring the Datadog Layer

The Datadog layer provides the [Datadog Agent][] and [ddtrace extension for PHP][].

When using this layer, you must set the following environment variables:

- `DD_API_KEY`

It may be necessary to set the following environment variables when running
PHP in a serverless environment:

- `DD_TRACE_CLI_ENABLED` - set this value to `1` (the default is `0`)

The following environment variables may be useful or necessary, depending on
how you use Datadog:

- `DD_SITE` - this defaults to `datadoghq.com`
- `DD_LOG_LEVEL` - e.g., `trace`, `debug`, `info`, `warn`, `error`, `critical`, `off`
- `DD_SERVICE` - the name of your service as it should appear in Datadog
- `DD_VERSION` - the version of your service, for display and filtering in Datadog
- `DD_ENV` - the environment your service is running in (i.e., staging, prod, etc.),
  for display and filtering in Datadog

Using environment variables, you may set any of the [Datadog Agent environment
variables][] or [PHP ddtrace environment variables][] in your `serverless.yml`
configuration, or you may configure them using a custom PHP INI file with Bref,
as described in the [Bref documentation][].

All values not set in your configuration will use the default [INI settings][]
for the extension.

See the [Datadog documentation][] for more information about serverless
monitoring for AWS Lambda.

[datadog agent]: https://docs.datadoghq.com/agent/
[ddtrace extension for php]: https://docs.datadoghq.com/tracing/trace_collection/dd_libraries/php/
[datadog agent environment variables]: https://docs.datadoghq.com/containers/docker/apm/?tab=linux#docker-apm-agent-environment-variables
[php ddtrace environment variables]: https://docs.datadoghq.com/tracing/trace_collection/library_config/php/#environment-variable-configuration
[bref documentation]: https://bref.sh/docs/environment/php.html
[ini settings]: https://docs.datadoghq.com/tracing/trace_collection/library_config/php/
[datadog documentation]: https://docs.datadoghq.com/serverless/aws_lambda
