<?php

namespace App\Service;

use DateTimeImmutable;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JWTService
{
    private $param;
    private $configuration;

    public function __construct(ParameterBagInterface $param)
    {
        $this->param = $param;
        $this->configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::base64Encoded($param->get('jwt.secret'))
        );
        $this->configuration->setValidationConstraints(
            new IssuedBy($param->get('jwt.issued_by')),
            new PermittedFor($param->get('jwt.permitted_for')),
            new ValidAt(SystemClock::fromUTC()),
            new SignedWith($this->configuration->signer(), $this->configuration->signingKey())
        );
    }

    public function createToken($userId): string
    {
        $now = new DateTimeImmutable();
        $token = $this->configuration->builder()
            ->issuedBy($this->param->get('jwt.issued_by'))
            ->permittedFor($this->param->get('jwt.permitted_for'))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+' . $this->param->get('jwt.expires_at')))
            ->withClaim('user_id', $userId)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey())
        ;

        return $token->toString();
    }

    public function validateToken($tokenStr): void
    {
        $token = null;
        try
        {
            $token = $this->configuration->parser()->parse($tokenStr);
        } catch (\Exception $ex) {
            throw new BadRequestException('Некорректный формат JWT');
        }
        $constraints = $this->configuration->validationConstraints();
        if (!$this->configuration->validator()->validate($token, ...$constraints)) {
            throw new UnauthorizedHttpException('', 'Недействительный JWT');
        }
    }

    public function parseToken($token): Token
    {
        return $this->configuration->parser()->parse($token);
    }
}
