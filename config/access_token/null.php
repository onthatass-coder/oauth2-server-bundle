<?php

declare(strict_types=1);

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use League\Bundle\OAuth2ServerBundle\Repository\AccessTokenRepository;
use League\Bundle\OAuth2ServerBundle\Repository\NullAccessTokenRepository;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('league.oauth2_server.repository.access_token', NullAccessTokenRepository::class)
        ->args([
            service(EventDispatcherInterface::class),
        ])
        ->alias(AccessTokenRepositoryInterface::class, 'league.oauth2_server.repository.access_token')
        ->alias(AccessTokenRepository::class, 'league.oauth2_server.repository.access_token');
};
