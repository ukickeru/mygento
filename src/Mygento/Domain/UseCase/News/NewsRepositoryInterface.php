<?php

namespace App\Mygento\Domain\UseCase\News;

use App\Mygento\Domain\Model\News\News;

interface NewsRepositoryInterface
{
    public function findOneById(string $id): ?News;

    /**
     * @return News[]|array
     */
    public function findAllNewsWithUsersLikes(): array;

    public function isNewsAlreadyLikedByUser(string $newsId, string $userId): bool;

    public function save(News $news): void;

    public function update(News $news): void;

    public function remove(string $id): int;
}