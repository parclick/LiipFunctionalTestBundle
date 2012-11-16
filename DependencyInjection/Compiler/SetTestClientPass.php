<?php

namespace Liip\FunctionalTestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Alias;

class SetTestClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (null === $container->getParameter('liip_functional_test.query_count.max_query_count')) {
            $container->removeDefinition('liip_functional_test.query_count.query_count_client');
            return;
        }

        if ($container->hasDefinition('test.client')) {
            // test.client is a definition.
            // Register it again as a private service to inject it as the parent
            $definition = $container->getDefinition('test.client');
            $definition->setPublic(false);
            $container->setDefinition('liip_functional_test.query_count.query_count_client.parent', $definition);
        } elseif ($container->hasAlias('test.client')) {
            // test.client is an alias.
            // Register a private alias for this service to inject it as the parent
            $container->setAlias(
                'liip_functional_test.query_count.query_count_client.parent',
                new Alias((string) $container->getAlias('test.client'), false)
            );
        } else {
            throw new \Exception('The LiipFunctionalTestBundle\'s Query Counter can only be used in the test environment.' . PHP_EOL . 'See https://github.com/liip/LiipFunctionalTestBundle#TODO');
        }

        $container->setAlias('test.client', 'liip_functional_test.query_count.query_count_client');
    }
}
