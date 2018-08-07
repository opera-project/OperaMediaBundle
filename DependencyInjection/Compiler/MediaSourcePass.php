<?php

namespace Opera\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opera\MediaBundle\MediaManager\MediaManager;
use Symfony\Component\DependencyInjection\Reference;

class MediaSourcePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $manager = $container->getDefinition(MediaManager::class);

        // or processing tagged services:
        foreach ($container->findTaggedServiceIds('opera.media_source') as $id => $tags) {
            $manager->addMethodCall('registerSource', [ new Reference($id) ]);   
        }
    }
}