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

use Symfony\Bundle\AclBundle\Command\InitAclCommand;
use Symfony\Bundle\AclBundle\Command\SetAclCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->private()
    ;

    $services->set(InitAclCommand::class)
        ->args([
            service('security.acl.dbal.connection'),
            service('security.acl.dbal.schema'),
        ])
        ->tag('console.command', ['command' => 'acl:init']);

    $services->set(SetAclCommand::class)
        ->args([service('security.acl.provider')])
        ->tag('console.command', ['command' => 'acl:set']);
};
