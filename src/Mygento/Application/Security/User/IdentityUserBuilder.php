<?php

namespace App\Mygento\Application\Security\User;

use App\Mygento\Domain\Model\User\IdentityUser\AbstractIdentityUserBuilder;
use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;

class IdentityUserBuilder extends AbstractIdentityUserBuilder
{
    public function build(): IdentityUserInterface
    {
        $user = new IdentityUser(
            $this->password,
            $this->user,
            $this->roles
        );

        $this->reset();

        return $user;
    }
}