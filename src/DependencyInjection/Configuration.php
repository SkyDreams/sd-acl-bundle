<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\AclBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('acl');
        $rootNode = $builder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('connection')
                    ->defaultNull()
                    ->info('any name configured in doctrine.dbal section')
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('id')->end()
                        ->scalarNode('pool')->info('The cache pool used to store ACLs')->end()
                        ->scalarNode('prefix')->defaultValue('sf_acl_')->end()
                    ->end()
                    ->validate()
                        ->ifTrue(static fn (array $config): bool => isset($config['id'], $config['pool']))
                        ->thenInvalid('You cannot set both a cache service id and cache pool')
                    ->end()
                ->end()
                ->scalarNode('provider')->end()
                ->arrayNode('tables')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('acl_classes')->end()
                        ->scalarNode('entry')->defaultValue('acl_entries')->end()
                        ->scalarNode('object_identity')->defaultValue('acl_object_identities')->end()
                        ->scalarNode('object_identity_ancestors')->defaultValue('acl_object_identity_ancestors')->end()
                        ->scalarNode('security_identity')->defaultValue('acl_security_identities')->end()
                    ->end()
                ->end()
                ->arrayNode('voter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('allow_if_object_identity_unavailable')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
