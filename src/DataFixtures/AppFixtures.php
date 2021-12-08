<?php

namespace App\DataFixtures;

use App\Mygento\Application\Security\User\IdentityUser;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use App\Mygento\Domain\UseCase\News\DTO\AddNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\AddUserLikeDTO;
use App\Mygento\Domain\UseCase\UseCase;
use App\Mygento\Domain\UseCase\User\DTO\AddUserDTO;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private UseCase $useCase;

    private Generator $faker;

    public function __construct(UseCase $useCase) {
        $this->useCase = $useCase;
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager): void
    {
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
            $news->title = $this->faker->realText(50);
            $news->content = $this->faker->realText(255);

            $news = $this->useCase->addNews($news);

            if ($i % 2 === 0) {
                $userIdThatLikeNews = $user1->id;
            } else {
                $userIdThatLikeNews = $user2->id;
            }

            $addUserLikeToNewsDTO = new AddUserLikeDTO();
            $addUserLikeToNewsDTO->userId = $userIdThatLikeNews;
            $addUserLikeToNewsDTO->newsId = $news->id;

            $this->useCase->addUserLikeToNews($addUserLikeToNewsDTO);
        }
    }
}
