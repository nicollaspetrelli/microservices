<?php
declare(strict_types=1);

use App\AclInterface;
use App\LaminasAcl\LaminasAcl;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AclInterface::class => function (ContainerInterface $c) {
            $acl = new LaminasAcl();

            $secretClass = "Secrets\Domain\Secret\Secret";
            $applicationClass = "Grades\Domain\Application\Application";

            $acl->addResource($secretClass);
            $acl->addResource($applicationClass);

            $acl->addRole('admin');
            $acl->allow('admin', $secretClass);
            $acl->allow('admin', $applicationClass);

            $acl->addRole('moderator');
            $acl->allow('moderator', $secretClass, '3');
            $acl->allow('moderator', $applicationClass, '2');

            return $acl;
        },
    ]);
};
