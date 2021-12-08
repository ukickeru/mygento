<?php

namespace App\Mygento\Domain\Model\User;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;
use App\Mygento\Domain\Model\User\ValueObject\Name;

interface UserInterface
{
    public function getId(): ?string;

    public function getName(): string;

    public function setName(Name $name): self;

    public function getLikedNews(): array;

    public function addLikedNews(News $news): self;

    public function removeLikedNews(News $news): self;

    public function getIdentityUser(): ?IdentityUserInterface;

    public function setIdentityUser(IdentityUserInterface $identityUser): self;
}
