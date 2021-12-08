<?php

namespace App\Mygento\Domain\UseCase\User\DTO;

use App\Mygento\Domain\Model\User\User;

class GetUserDTO
{
    public ?string $id = null;

    public string $name;

    public array $roles = [];

    public array $likedNews = [];

    public static function createFromUser(User $user): self
    {
        $userDTO = new self();
        $userDTO->id = $user->getId();
        $userDTO->name = $user->getName();
        $userDTO->roles = $user->getIdentityUser()->getRoles();
        $userDTO->likedNews = $user->getLikedNews();
        return $userDTO;
    }
}