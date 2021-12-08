<?php

namespace App\Mygento\Domain\Model\News;

use App\Mygento\Domain\Model\News\ValueObject\Content;
use App\Mygento\Domain\Model\News\ValueObject\Title;
use App\Mygento\Domain\Model\User\User;

interface NewsInterface
{
    public function getId(): ?string;

    public function getTitle(): string;

    public function setTitle(Title $title): self;

    public function getContent(): string;

    public function setContent(Content $content): self;

    public function getLikedUsers(): array;

    public function addLikedUser(User $user): self;

    public function removeLikedUser(User $user): self;
}
