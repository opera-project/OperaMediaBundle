<?php

namespace Opera\MediaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Opera\MediaBundle\MediaManager\Source;
use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\MediaRepository;

class OperaMediaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config =  $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        foreach ($config['sources'] as $name => $source) {
            $definition = new Definition(Source::class);
            $definition->addArgument(new Reference($source['filesystem']));
            $definition->addArgument($name);
            $definition->addArgument(new Reference(FolderRepository::class));
            $definition->addArgument(new Reference(MediaRepository::class));

            $definition->addTag('opera.media_source');

            $container->setDefinition('opera.media_source.'.$name, $definition);
        }

    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias());
    }
}
