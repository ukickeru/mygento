<?php

namespace App\Tests\Mygento\Domain\UseCase;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\News\ValueObject\Title;
use App\Mygento\Domain\Model\News\ValueObject\UUID as NewsID;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use App\Mygento\Domain\Model\User\ValueObject\UUID as UserID;
use App\Mygento\Domain\UseCase\News\DTO\AddNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\AddUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\EditNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\GetNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\RemoveUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\UserLikeDTO;
use App\Mygento\Domain\UseCase\News\NewsRepositoryInterface;
use App\Mygento\Domain\UseCase\UseCase;
use App\Mygento\Domain\UseCase\User\DTO\AddUserDTO;
use App\Mygento\Domain\UseCase\User\DTO\EditUserDTO;
use App\Mygento\Domain\UseCase\User\DTO\GetUserDTO;
use App\Mygento\Domain\UseCase\User\UserRepositoryInterface;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\DoctrineRepositoryTestCase;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\News\CreateNewsTrait;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\User\CreateUserTrait;
use Faker\Factory;

/**
 * @covers UseCase
 */
final class UseCaseTest extends DoctrineRepositoryTestCase
{
    use CreateUserTrait;

    use CreateNewsTrait;

    protected UseCase $useCase;

    protected UserRepositoryInterface $userRepository;

    protected NewsRepositoryInterface $newsRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = $this::$kernel->getContainer()->get('mygento.domain.use-case');
        $this->useCase = $this::$kernel->getContainer()->get('mygento.domain.use-case');
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->newsRepository = $this->entityManager->getRepository(News::class);
    }

    private function createAndSaveUser(): User
    {
        $identityUser = self::createIdentityUser();
        $user = $identityUser->getDomainUser();

        $this->userRepository->save($user);

        return $user;
    }

    private function createAndSaveNews(): News
    {
        $news = self::createNews();

        $this->newsRepository->save($news);

        return $news;
    }

    private function createUsersAndLikedNews()
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

            $addUserLikeToNewsDTO = new AddUserLikeDTO();
            $addUserLikeToNewsDTO->userId = $userIdThatLikeNews;
            $addUserLikeToNewsDTO->newsId = $news->id;

            $this->useCase->addUserLikeToNews($addUserLikeToNewsDTO);
        }
    }

    /**
     * @covers UseCase::getUser
     */
    public function testGetUser()
    {
        $user = $this->createAndSaveUser();
        $identityUser = $user->getIdentityUser();

        $userId = $user->getId();
        $userName = $user->getName();
        $userRoles = $identityUser->getRoles();

        $this->entityManager->clear();
        unset($user, $identityUser);

        $userDTO = $this->useCase->getUser(new UserID($userId));
        $this->assertInstanceOf(GetUserDTO::class, $userDTO);
        $this->assertEquals($userId, $userDTO->id);
        $this->assertEquals($userName, $userDTO->name);
        $this->assertEquals($userRoles, $userDTO->roles);
    }

    /**
     * @covers UseCase::getUser
     */
    public function testAddUser()
    {
        $addUserDTO = new AddUserDTO();
        $addUserDTO->name = 'Username';
        $addUserDTO->plainPassword = 'password';

        $newUserDTO = $this->useCase->addUser($addUserDTO);

        $this->assertInstanceOf(GetUserDTO::class, $newUserDTO);
        $this->assertEquals($addUserDTO->name, $newUserDTO->name);
    }

    /**
     * @covers UseCase::editUser
     */
    public function testEditUser()
    {
        $user = $this->createAndSaveUser();
        $identityUser = $user->getIdentityUser();

        $this->userRepository->save($user);

        /** @var string $userId */
        $userId = $user->getId();
        $newUsername = new Name('AnotherName');

        $editUserDTO = new EditUserDTO();
        $editUserDTO->id = $userId;
        $editUserDTO->name = $newUsername;
        $editUserDTO->password = $newUsername;

        $this->useCase->editUser($editUserDTO);

        $this->entityManager->clear();
        unset($user, $identityUser);

        $foundUser = $this->userRepository->findOneById($userId);
        $this->assertEquals($newUsername, $foundUser->getName());
    }

    /**
     * @covers UseCase::removeUser
     */
    public function testRemoveUser()
    {
        $user = $this->createAndSaveUser();
        $identityUser = $user->getIdentityUser();

        $userId = $user->getId();

        $this->entityManager->clear();
        unset($user, $identityUser);

        $deletedUserCount = $this->useCase->removeUser(new UserID($userId));
        $this->assertEquals(1, $deletedUserCount);

        $userNotFound = $this->userRepository->findOneById($userId);
        $this->assertEquals(null, $userNotFound);
    }

    /**
     * @covers UseCase::getAllNewsWithUsersLikes
     */
    public function testGetAllNewsWithUsersLikes()
    {
        $this->createUsersAndLikedNews();

        $result = $this->newsRepository->findAllNewsWithUsersLikes();

        $this->assertIsArray($result);
        $this->assertInstanceOf(News::class, $result[0]);
    }

    /**
     * @covers UseCase::getNews
     */
    public function testGetNews()
    {
        $news = $this->createAndSaveNews();

        $newsId = $news->getId();
        $newsTitle = $news->getTitle();
        $newsContent = $news->getContent();

        $this->entityManager->clear();
        unset($news);

        $newsDTO = $this->useCase->getNews(new NewsID($newsId));
        $this->assertInstanceOf(GetNewsDTO::class, $newsDTO);
        $this->assertEquals($newsId, $newsDTO->id);
        $this->assertEquals($newsTitle, $newsDTO->title);
        $this->assertEquals($newsContent, $newsDTO->content);
    }

    /**
     * @covers UseCase::addNews
     */
    public function testAddNews()
    {
        $addNewsDTO = new AddNewsDTO();
        $addNewsDTO->title = 'Title';
        $addNewsDTO->content = 'Content';

        $newNewsDTO = $this->useCase->addNews($addNewsDTO);

        $this->assertInstanceOf(GetNewsDTO::class, $newNewsDTO);
        $this->assertEquals($addNewsDTO->title, $newNewsDTO->title);
    }

    /**
     * @covers UseCase::editNews
     */
    public function testEditNews()
    {
        $news = $this->createAndSaveNews();

        $this->newsRepository->save($news);

        /** @var string $newsId */
        $newsId = $news->getId();
        $newTitle = new Title('AnotherTitle');
        $newContent = new Title('AnotherContent');

        $editNewsDTO = new EditNewsDTO();
        $editNewsDTO->id = $newsId;
        $editNewsDTO->title = $newTitle;
        $editNewsDTO->content = $newContent;

        $this->useCase->editNews($editNewsDTO);

        $this->entityManager->clear();
        unset($news);

        $foundNews = $this->newsRepository->findOneById($newsId);
        $this->assertEquals($newTitle, $foundNews->getTitle());
        $this->assertEquals($newContent, $foundNews->getContent());
    }

    /**
     * @covers UseCase::removeNews
     */
    public function testRemoveNews()
    {
        $news = $this->createAndSaveNews();

        $newsId = $news->getId();

        $this->entityManager->clear();
        unset($user, $identityUser);

        $deletedNewsCount = $this->useCase->removeNews(new NewsID($newsId));
        $this->assertEquals(1, $deletedNewsCount);

        $newsNotFound = $this->userRepository->findOneById($newsId);
        $this->assertEquals(null, $newsNotFound);
    }

    /**
     * @covers UseCase::toggleUserLike
     */
    public function testToggleUserLike()
    {
        $user = $this->createAndSaveUser();
        $news = $this->createAndSaveNews();

        $userLikeDTO = new UserLikeDTO();
        $userLikeDTO->userId = $user->getId();
        $userLikeDTO->newsId = $news->getId();

        $isAlreadyLiked = $this->newsRepository->isNewsAlreadyLikedByUser($news->getId(), $user->getId());
        $this->assertEquals(false, $isAlreadyLiked);

        $this->useCase->toggleUserLike($userLikeDTO);

        $isAlreadyLiked = $this->newsRepository->isNewsAlreadyLikedByUser($news->getId(), $user->getId());
        $this->assertEquals(true, $isAlreadyLiked);

        $this->useCase->toggleUserLike($userLikeDTO);

        $isAlreadyLiked = $this->newsRepository->isNewsAlreadyLikedByUser($news->getId(), $user->getId());
        $this->assertEquals(false, $isAlreadyLiked);
    }
}
