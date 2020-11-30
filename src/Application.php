<?php declare(strict_types=1);

namespace Bref\Extra;

use AsyncAws\Core\HttpClient\AwsRetryStrategy;
use AsyncAws\Lambda\LambdaClient;
use Bref\Extra\Aws\LayerProvider;
use Bref\Extra\Aws\LayerPublisher;
use Bref\Extra\Command\ListCommand;
use Bref\Extra\Command\PublishCommand;
use Bref\Extra\Service\RegionProvider;
use Bref\Logger\StderrLogger;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application extends \Silly\Edition\PhpDi\Application
{
    protected function createContainer(): Container
    {
        $builder = new ContainerBuilder;
        $projectDir = dirname(__DIR__);
        $localLayers = array_keys(json_decode(file_get_contents($projectDir . '/checksums.json'), true));

        $builder->addDefinitions([
            'project_dir' => $projectDir,
            'layer_names' => $localLayers,
            LoggerInterface::class => function (ContainerInterface $c) {
                return new StderrLogger(LogLevel::INFO);
            },
            LayerProvider::class => function (ContainerInterface $c) {
                return new LayerProvider($c->get(LambdaClient::class), $c->get('layer_names'));
            },
            ListCommand::class => function (ContainerInterface $c) {
                return new ListCommand($c->get(LayerProvider::class), $c->get(RegionProvider::class), $c->get('project_dir'));
            },
            PublishCommand::class => function (ContainerInterface $c) {
                return new PublishCommand($c->get(LayerPublisher::class), $c->get(RegionProvider::class), $c->get('project_dir'));
            },
            HttpClientInterface::class => function (ContainerInterface $c) {
                $strategy = new AwsRetryStrategy(AwsRetryStrategy::DEFAULT_RETRY_STATUS_CODES, 5000, 2, 600000);

                return new RetryableHttpClient(HttpClient::create(), $strategy, 12, $c->get(LoggerInterface::class));
            },
            LambdaClient::class => function (ContainerInterface $c) {
                return new LambdaClient([], null, $c->get(HttpClientInterface::class), $c->get(LoggerInterface::class));
            },
            LayerPublisher::class => function (ContainerInterface $c) {
                return new LayerPublisher($c->get(LambdaClient::class));
            },
        ]);

        return $builder->build();
    }
}
