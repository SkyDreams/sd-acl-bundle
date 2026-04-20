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

use Symfony\Bundle\AclBundle\EventListener\AclSchemaListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Dbal\Schema;
use Symfony\Component\Security\Acl\Domain\DoctrineAclCache;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->private()
    ;

    $tables = [
        'class_table_name' => '%security.acl.dbal.class_table_name%',
        'entry_table_name' => '%security.acl.dbal.entry_table_name%',
        'oid_table_name' => '%security.acl.dbal.oid_table_name%',
        'oid_ancestors_table_name' => '%security.acl.dbal.oid_ancestors_table_name%',
        'sid_table_name' => '%security.acl.dbal.sid_table_name%',
    ];

    $services->alias('security.acl.dbal.connection', 'database_connection')->public();

    $services->set('security.acl.dbal.provider', MutableAclProvider::class)
        ->args([
            service('security.acl.dbal.connection'),
            service('security.acl.permission_granting_strategy'),
            $tables,
            service('security.acl.cache')->nullOnInvalid(),
        ]);

    $services->set('security.acl.dbal.schema', Schema::class)
        ->public()
        ->args([
            $tables,
            service('security.acl.dbal.connection'),
        ]);

    $services->set('security.acl.dbal.schema_listener', AclSchemaListener::class)
        ->args([service('security.acl.dbal.schema')]);

    $services->alias('security.acl.provider', 'security.acl.dbal.provider')->public();
    $services->alias(AclProviderInterface::class, 'security.acl.provider');

    $services->set('security.acl.cache.doctrine', DoctrineAclCache::class)
        ->args([
            service('security.acl.cache.doctrine.cache_impl'),
            service('security.acl.permission_granting_strategy'),
        ]);

    $services->alias('security.acl.cache.doctrine.cache_impl', 'doctrine.orm.default_result_cache');
};
