<?php

namespace App\Tests\Mygento\Infrastructure\Repository\Doctrine\News;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\UseCase\News\DTO\AddNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\UserLikeDTO;
use App\Mygento\Domain\UseCase\News\NewsRepositoryInterface;
use App\Mygento\Domain\UseCase\UseCase;
use App\Mygento\Domain\UseCase\User\DTO\AddUserDTO;
use App\Mygento\Infrastructure\Repository\Doctrine\News\NewsRepository;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\DoctrineRepositoryTestCase;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\User\CreateUserTrait;
use Faker\Factory;

/**
 * @covers \App\Mygento\Infrastructure\Repository\Doctrine\News\NewsRepository
 */
class NewsRepositoryTest extends DoctrineRepositoryTestCase
{
    use CreateNewsTrait;

    use CreateUserTrait;

    protected UseCase $useCase;

    protected NewsRepositoryInterface $newsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = $this::$kernel->getContainer()->get('mygento.domain.use-case');
        $this->newsRepository = $this->entityManager->getRepository(News::class);
    }

    private function createAndSaveUsersAndLikedNews()
    {
        $faker = Factory::create();

        $user1 = new AddUserDTO();
        $user1->name = 'UserOne';
        $user1->plainPassword = 'password1';

        $user1 = $this->useCase->addUser($user1);

        $user2 = new AddUserDTO();
        $user2->name = 'UserTwo';
        $user2->plainPassword = 'password2';

        $user2 = $this->useCase->addUser($user2);

        for ($i = 0; $i < 10; $i++) {
            $news = new AddNewsDTO();
            $news->title = $faker->realText(50);
            $news->content = $faker->realText(255);

            $news = $this->useCase->addNews($news);

            if ($i % 2 === 0) {
                $userIdThatLikeNews = $user1->id;
            } else {
                $userIdThatLikeNews = $user2->id;
            }

            $userLikeDTO = new UserLikeDTO();
            $userLikeDTO->userId = $userIdThatLikeNews;
            $userLikeDTO->newsId = $news->id;

            $this->useCase->toggleUserLike($userLikeDTO);
        }
    }

    /**
     * @covers NewsRepository::findAllNewsWithUsersLikes
     */
    public function testFindAllNewsWithUsersLikes()
    {
        $this->createAndSaveUsersAndLikedNews();

        $allNews = $this->newsRepository->findAllNewsWithUsersLikes();

        $this->assertNotEmpty($allNews);
        $this->assertInstanceOf(News::class, $allNews[0]);
    }
}
