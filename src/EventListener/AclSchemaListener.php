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

namespace Symfony\Bundle\AclBundle\EventListener;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Symfony\Component\Security\Acl\Dbal\Schema;

/**
 * Merges ACL schema into the given schema.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class AclSchemaListener
{
    public function __construct(private readonly Schema $schema)
    {
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $this->schema->addToSchema($args->getSchema());
    }
}
