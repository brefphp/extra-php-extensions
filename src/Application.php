<?php

declare(strict_types=1);

namespace Bref\Extra;

use Bref\Extra\Aws\LayerProvider;
use Bref\Extra\Aws\LayerPublisher;
use Bref\Extra\Service\RegionProvider;
use Bref\Extra\Command\ListCommand;
use Bref\Extra\Command\PublishCommand;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Application extends \Silly\Edition\PhpDi\Application
{
    protected function createContainer()
    {
        $builder = new ContainerBuilder();
        $awsId = getenv('AWS_ID');
        $awsProfile = getenv('AWS_PROFILE');
        if (empty($awsId)){
            $awsId = 'xxxxxxxxx';
        }
        if (empty($awsProfile)){
            $awsProfile = null;
        }

        $projectDir = dirname(__DIR__);
        $localLayers = array_keys(json_decode(file_get_contents($projectDir.'/checksums.json'), true));

        $builder->addDefinitions([
            'project_dir' => $projectDir,
            'aws_id' => $awsId,
            'aws_profile' => $awsProfile,
            'layer_names' => $localLayers,
            LayerProvider::class => function (ContainerInterface $c) {
                return new LayerProvider($c->get('layer_names'), $c->get('aws_id'));
            },
            ListCommand::class => function (ContainerInterface $c) {
                return new ListCommand($c->get(LayerProvider::class), $c->get(RegionProvider::class), $c->get('project_dir'));
            },
            PublishCommand::class => function (ContainerInterface $c) {
                return new PublishCommand($c->get(LayerPublisher::class), $c->get(RegionProvider::class), $c->get('project_dir'));
            },
            LayerPublisher::class => function (ContainerInterface $c) {
                return new LayerPublisher($c->get('aws_profile'));
            },
        ]);

        return $builder->build();
    }
}