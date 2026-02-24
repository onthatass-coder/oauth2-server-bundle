<?php

declare(strict_types=1);

namespace League\Bundle\OAuth2ServerBundle\Entity;

use Lcobucci\JWT\Token;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

final class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    /**
     * @var non-empty-string|null
     */
    private ?string $issuer = null;

    /**
     * @var array<non-empty-string, string|int|bool>
     */
    protected array $customClaims = [];

    /**
     * @var array<non-empty-string, string|int|bool>
     */
    protected array $customHeaders = [];

    /**
     * @return non-empty-string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * @param non-empty-string|null $issuer
     */
    public function setIssuer(?string $issuer): void
    {
        $this->issuer = $issuer;
    }

    /**
     * @return array<non-empty-string, string|int|bool>
     */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * @param array<non-empty-string, string|int|bool> $customHeaders
     */
    public function setCustomHeaders(array $customHeaders): void
    {
        $this->customHeaders = $customHeaders;
    }

    /**
     * @param array<non-empty-string, string|int|bool> $customClaims
     */
    public function setCustomClaims(array $customClaims): void
    {
        $this->customClaims = $customClaims;
    }

    /**
     * @return array<non-empty-string, string|int|bool>
     */
    public function getCustomClaims(): array
    {
        return $this->customClaims;
    }

    public function convertToJWT(): Token
    {
        $this->initJwtConfiguration();

        $builder = $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new \DateTimeImmutable())
            ->canOnlyBeUsedAfter(new \DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($this->getSubjectIdentifier());

        if (null !== $this->issuer) {
            $builder = $builder->issuedBy($this->issuer);
        }

        foreach ($this->getCustomClaims() as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        foreach ($this->getCustomHeaders() as $key => $value) {
            $builder = $builder->withHeader($key, $value);
        }

        if (null !== $this->getNonce()) {
            $builder = $builder->withClaim('nonce', $this->getNonce()) ;
        }

        return $builder
            ->withClaim('scopes', $this->getScopes()) // We don't allow overriding scopes via custom claims
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}
