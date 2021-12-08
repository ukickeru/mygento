<?php

namespace App\Mygento\Domain\UseCase\User;

use App\Mygento\Domain\Model\User\User;

interface UserRepositoryInterface
{
    public function findOneById(string $id): ?User;

    public function save(User $user): void;

    public function update(User $user): void;

    public function remove(string $id): int;
}
