<?php

namespace App\Tests\Mygento\Infrastructure\Repository\Doctrine\User;

use App\Mygento\Application\Security\User\IdentityUser;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\Name;

trait CreateUserTrait
{
    public static function createDomainUser(): User
    {
        return new User(new Name('Username'));
    }

    public static function createIdentityUser(): IdentityUser
    {
        $user = self::createDomainUser();

        return new IdentityUser('password', $user);
    }
}
