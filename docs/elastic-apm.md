# Elastic APM Extension

### Background

[Elastic APM](https://www.elastic.co/observability/application-performance-monitoring) is a performance monitoring
solution that has an agent / SDK for instrumenting multiple different languages, including PHP.

The PHP agent requires BOTH an extension to be installed (for instrumenting internals / function calls), and their
[PHP library](https://github.com/elastic/apm-agent-php) available to your code for triggering the beginning and end
of transactions, and adding additional metadata.

This works "out of the box" with Lambda and Bref, but the transmission of telemetry from the extension has a performance
impact as it can block your request cycle while PHP sends this data to the APM server. Elastic provide an "experimental"
Lambda Extension that allows asynchronous flushing of telemetry from the PHP extension. More
information on this is available in the Lambda Extension
[documentation](https://www.elastic.co/guide/en/apm/guide/current/aws-lambda-arch.html).

### Installing & Configuring

You should install the extension in the same manner as any other extension available via this repository, and then
configure the settings according to the
[PHP agent documentation](https://www.elastic.co/guide/en/apm/agent/php/current/configuration.html).

For example, you could set the following in `<project>/php/elastic.ini`

```
elastic_apm.log_level=error
elastic_apm.server_timeout=2s
elastic_apm.transaction_max_spans=100
elastic_apm.bootstrap_php_part_file=/var/task/vendor/elastic/apm-agent/src/bootstrap_php_part.php
```

Depending on your use case, it may be worth using Lambda environment variables over ini settings for any that change between
environments.

```
ELASTIC_APM_SERVICE_NAME: 'Bref Project'
ELASTIC_APM_SERVER_URL: 'https://your-elastic-apm-server:443'
ELASTIC_APM_SECRET_TOKEN: 'your-token'
ELASTIC_APM_LOG_LEVEL: 'error'
```

If you'd like to take advantage of the Lambda Extension from Elastic, your environment variables should reflect the
below configuration. This will configure the PHP extension to send telemetry to the Lambda Extension, and configure the
Lambda extension to forward them to your APM server.

Information on adding the extension to your function layers is available in the
[Elastic documentation](https://www.elastic.co/guide/en/apm/agent/nodejs/current/lambda.html).

```
ELASTIC_APM_SERVICE_NAME: 'Bref Project'
ELASTIC_APM_LAMBDA_APM_SERVER: 'https://your-elastic-apm-server:443'
ELASTIC_APM_SERVER_URL: 'https://localhost:8200' // this is a default and can be omitted
ELASTIC_APM_SECRET_TOKEN: 'your-token'
ELASTIC_APM_SEND_STRATEGY: 'background'
ELASTIC_APM_LOG_LEVEL: 'error'
```
