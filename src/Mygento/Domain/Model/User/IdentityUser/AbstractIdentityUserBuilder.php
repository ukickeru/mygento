<?php

namespace App\Mygento\Domain\Model\User\IdentityUser;

use App\Mygento\Domain\Model\User\UserInterface as DomainUserInterface;

abstract class AbstractIdentityUserBuilder
{
    protected array $roles = [];

    protected ?string $password = null;

    protected ?DomainUserInterface $user = null;

    public function __construct()
    {
        $this->reset();
    }

    abstract public function build(): IdentityUserInterface;

    public function reset()
    {
        $this->roles = [];
        $this->password = null;
        $this->user = null;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setUser(DomainUserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
