# Bref New Relic Extension

## Install and configure

Install the PHP extension like any other extension. When you added the layer, you
need add some extra php configuration for you License Key and Daemon Address.
The License Key should be your New Relic account License Key to ensure this app
reports to your account. The daemon address should be the location of a remote New
Relic PHP Daemon listening for an agent connection. This can be run in a layer if
desired but can lead to race conditions in cold starts. So a ready, listening
daemon running on a container or available machine is advisable.

You should configure your settings for the New Relic PHP Agent in `php/conf.d/newrelic-settings.ini`
within your Bref application. This will mean that the configuration of your agent is
controlled in your deployed application code.

Below are some suggested configuration settings to use.

```ini
newrelic.appname = BrefApp
newrelic.license = ceaxxxxxxxxxxxxxxxxxxxxceafe3
newrelic.daemon.address = xx.xx.xxx.xxx:31339

# Log to Cloud Watch
newrelic.logfile = /proc/self/fd/2
```

You can find all available options in the [New Relic documentation](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-configuration).

### Can I Use Environment Variables?

If you use a Key Management Service (KMS) or want to define the values as environment
variables in Lambda (through UI or a serverless.yml) You can use ENV VARS in your
configuration file which could be achieved as an example below.

```ini
newrelic.appname = ${NEW_RELIC_APP_NAME}
newrelic.license = ${NEW_RELIC_LICENSE_KEY}
newrelic.daemon.address = ${NEW_RELIC_DAEMON_ADDRESS}
newrelic.logfile = ${NEW_RELIC_LOG_FILE}
newrelic.loglevel = ${NEW_RELIC_LOG_LEVEL}
newrelic.daemon.app_connect_timeout = ${NEW_RELIC_DAEMON_APP_CONNECT_TIMEOUT}
newrelic.daemon.start_timeout = ${NEW_RELIC_DAEMON_START_TIMEOUT}
```

After changing your configuration to reference the ENVIRONMENT VARIABLES, you can
then set their values in Lambda and replace them using your KMS. This provides additional
security to your deployed code over using the license key. If using a KMS it
will make it easy to globally change across numerous Lambda's the location of
a daemon, the log level or the license key if you need to change where the data
reports.

## A Standalone Daemon

In this example, the New Relic Daemon will be running on ECS or an EC2 instance
listening on port 31339. If you follow the New Relic documentation and just install
the daemon to a host, you can have it listen for incoming connectivity.

You should follow the installation docs to enable the
[relevant repository](https://docs.newrelic.com/docs/agents/php-agent/advanced-installation/using-newrelic-install-script).


Then use install_daemon mode to only install the daemon to this machine.

```cli
sudo newrelic-install install_daemon
```

Once installed you can start it and tell it the port to listen on. Ensuring the
agent will be able to reach it.

```cli
/usr/bin/newrelic-daemon --address=0.0.0.0:31339
```

You may need to configure your Lambda to be in a VPC with public/private subnets
allowing access to an Internet Gateway to reach the machine or potentially house
the machines in the same VPC for local network communication.

If you wish to run the daemon as a container. [This repository](https://github.com/newrelic/newrelic-php-daemon-docker)
should be particularly useful.


The same logic applies that the container must be reachable by the Lambda so configuring
your security settings to allow the Lambda to reach the daemon will be important.
If you use STDERR as suggested above, the Cloudwatch logs will be a useful guide
if you are not having success connecting.
