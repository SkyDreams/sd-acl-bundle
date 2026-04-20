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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;
use Symfony\Component\Security\Acl\Voter\AclVoter;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->private()
    ;

    $services->set('security.acl.object_identity_retrieval_strategy', ObjectIdentityRetrievalStrategy::class);

    $services->set('security.acl.security_identity_retrieval_strategy', SecurityIdentityRetrievalStrategy::class)
        ->args([
            service('security.role_hierarchy'),
            service('security.authentication.trust_resolver'),
        ]);

    $services->set('security.acl.permission_granting_strategy', PermissionGrantingStrategy::class)
        ->call('setAuditLogger', [service('security.acl.audit_logger')->ignoreOnInvalid()]);

    $services->set('security.acl.permission.map', BasicPermissionMap::class);

    $services->set('security.acl.voter.basic_permissions', AclVoter::class)
        ->args([
            service('security.acl.provider'),
            service('security.acl.object_identity_retrieval_strategy'),
            service('security.acl.security_identity_retrieval_strategy'),
            service('security.acl.permission.map'),
            service('logger')->nullOnInvalid(),
        ])
        ->tag('monolog.logger', ['channel' => 'security'])
        ->tag('security.voter', ['priority' => 255]);
};
