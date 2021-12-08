<?php

namespace App\Mygento\Domain\Model\User\IdentityUser;

use App\Mygento\Domain\Model\User\User;

interface IdentityUserInterface
{
    public function getId(): ?string;

    public function getRoles(): array;

    public function setRoles(array $roles): self;

    public function getPassword(): string;

    public function setPassword(string $plainPassword): self;

    public function getDomainUser(): User;

    public function getDomainUserId(): ?string;

    public function getDomainUserName(): ?string;
}
