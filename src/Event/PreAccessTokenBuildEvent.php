<?php

declare(strict_types=1);

namespace League\Bundle\OAuth2ServerBundle\Event;

use League\Bundle\OAuth2ServerBundle\Entity\AccessToken;
use Symfony\Contracts\EventDispatcher\Event;

class PreAccessTokenBuildEvent extends Event
{
    public function __construct(
        private readonly AccessToken $accessToken,
    ) {
    }

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }
}
