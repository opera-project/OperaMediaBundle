<?php

namespace Opera\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opera\MediaBundle\DependencyInjection\Compiler\MediaSourcePass;
use Opera\MediaBundle\DependencyInjection\Compiler\ResourceCompilerPass;


class OperaMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MediaSourcePass());
        $container->addCompilerPass(new ResourceCompilerPass());

    }
}
