### Datadog installation

This layer enables the installation of only the Datadog PHP extension.

To use the extension, you must also install the Datadog agent.
You can achieve this by installing another Datadog Lambda Extension layer.
For more information see https://docs.datadoghq.com/serverless/installation/python/?tab=custom the "Install the Datadog Lambda Extension" section.

If you are running an x86-based Lambda in AWS commercial regions, use the following ARN:
`arn:aws:lambda:<AWS_REGION>:464622532012:layer:Datadog-Extension:<version>`

If you are running an ARM-based Lambda in AWS commercial regions, use the following ARN:
`arn:aws:lambda:<AWS_REGION>:464622532012:layer:Datadog-Extension-ARM:<version>`

## Configuration

After installing the layer and the agent, you must configure the Datadog extension by adding the following key/value pair to a Lambda environment variable:

- `DD_ENV=<enviroment>`
- `DD_SERVICE=<service>`
- `DD_SITE=<datadoghq.eu|datadoghq.com>` (depending on your Datadog account region)
- `DD_API_KEY=<api_key>`
- `DD_SERVICE=<your service name>`
- `DD_VERSION=<your service version>`

For more details about the configuration, see https://docs.datadoghq.com/tracing/trace_collection/library_config/php/

You should also consider adding similar AWS tags to your Lambda function to link APM and Infrastructure data together using the tag/value syntax:

- `Env=<the same as DD_ENV>`
- `Service=<the same as DD_SERVICE>`
- `Team=<the team name>` (use `a-z0-9\-` pattern)
- `Tier=<A|B|C|...>` (to indicate how important the service is)

Find more about infrastructure tagging:
 - https://www.datadoghq.com/blog/tagging-best-practices/
 - https://learn.datadoghq.com/courses/tagging-best-practices (online course)
 - Enroll in free technical sessions at https://www.datadoghq.com/technical-enablement/sessions/

## Custom instrumentation

DataDog works out of the box with the php-xx-fpm runtime. However, if you are using the php-xx runtime and BREF_LOOP_MAX>1, you must add custom instrumentation.
Otherwise, DataDog will wait until the end of a loop and only send one trace.
To add custom instrumentation, create an `instrumentation.php` file and add the following code:

```php
<?php

\DDTrace\trace_method(
    'Bref\Runtime\Invoker',
    'invoke',
    function (\DDTrace\SpanData $span, $args, $ret, $exception) {
        $span->service = getenv('DD_SERVICE');
        $span->type = \DDTrace\Type::CLI;
        $span->name = 'invoke';
    }
);
```

This code will enable you to see all traces.

*do not forget to add `instrumentation.php` to a `composer.json` file

For EventBridge Lambdas, add the following code to the function above:

```php
<?php
    if ($args[0] instanceof \Bref\Event\EventBridge\EventBridgeEvent) {
        $span->resource = $args[0]->getDetailType();
    }
```

This code will name your traces with the EventBridge event name.

For more information about custom instrumentation, refer to the following resources:

 - https://github.com/DataDog/dd-trace-php/blob/master/examples/long-running/long-running-script.php
 - https://docs.datadoghq.com/tracing/trace_collection/custom_instrumentation/php/?tab=currentspan
 - to link traces with a parent one: https://docs.datadoghq.com/tracing/trace_collection/trace_context_propagation/php/

## service.datadog.yaml

Consider also creating a `service.datadog.yaml` file in the root of your project.
It will give you more control over the traces and ability to add links to documentation and contact information.

To find more: https://www.datadoghq.com/blog/manage-service-catalog-categories-with-service-definition-json-schema/
