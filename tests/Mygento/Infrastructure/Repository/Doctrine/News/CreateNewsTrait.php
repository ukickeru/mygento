<?php

namespace App\Tests\Mygento\Infrastructure\Repository\Doctrine\News;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\News\ValueObject\Content;
use App\Mygento\Domain\Model\News\ValueObject\Title;

trait CreateNewsTrait
{
    public static function createNews(): News
    {
        return new News(
            new Title('Title'),
            new Content('Content')
        );
    }
}
