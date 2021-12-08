<?php

namespace App\Mygento\Domain\UseCase\News\DTO;

use App\Mygento\Domain\Model\News\News;

class GetNewsDTO
{
    public ?string $id = null;

    public string $title;

    public string $content;

    public array $likedUsers = [];

    public static function createFromNews(News $news): self
    {
        $newsDTO = new self();
        $newsDTO->id = $news->getId();
        $newsDTO->title = $news->getTitle();
        $newsDTO->content = $news->getContent();
        $newsDTO->likedUsers = $news->getLikedUsers();
        return $newsDTO;
    }
}