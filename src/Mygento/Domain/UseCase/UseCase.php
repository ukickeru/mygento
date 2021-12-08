<?php

namespace App\Mygento\Domain\UseCase;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\News\ValueObject\Content;
use App\Mygento\Domain\Model\News\ValueObject\Title;
use App\Mygento\Domain\Model\News\ValueObject\UUID as NewsUUID;
use App\Mygento\Domain\Model\User\IdentityUser\AbstractIdentityUserBuilder;
use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use App\Mygento\Domain\Model\User\ValueObject\UUID as UserUUID;
use App\Mygento\Domain\UseCase\News\DTO\AddNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\AddUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\EditNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\GetNewsDTO;
use App\Mygento\Domain\UseCase\News\DTO\RemoveUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\UserLikeDTO;
use App\Mygento\Domain\UseCase\News\NewsRepositoryInterface;
use App\Mygento\Domain\UseCase\User\DTO\AddUserDTO;
use App\Mygento\Domain\UseCase\User\DTO\EditUserDTO;
use App\Mygento\Domain\UseCase\User\DTO\GetUserDTO;
use App\Mygento\Domain\UseCase\User\UserRepositoryInterface;
use DomainException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UseCase
{
    private UserRepositoryInterface $userRepository;

    private NewsRepositoryInterface $newsRepository;

    private AbstractIdentityUserBuilder $identityUserBuilder;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        NewsRepositoryInterface $newsRepository,
        AbstractIdentityUserBuilder $identityUserBuilder,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->newsRepository = $newsRepository;
        $this->identityUserBuilder = $identityUserBuilder;
        $this->passwordHasher = $passwordHasher;
    }

    public function getUser(UserUUID $userId): GetUserDTO
    {
        $user = $this->userRepository->findOneById($userId);

        return GetUserDTO::createFromUser($user);
    }

    public function addUser(AddUserDTO $userDTO): GetUserDTO
    {
        $newUser = new User(new Name($userDTO->name));

        $newIdentityUser = $this->identityUserBuilder
            ->setPassword($userDTO->plainPassword)
            ->setUser($newUser)
            ->build();

        $this->updateIdentityUserPassword($newIdentityUser, $userDTO->plainPassword);

        $this->userRepository->save($newUser);

        return GetUserDTO::createFromUser($newUser);
    }

    public function editUser(EditUserDTO $userDTO): GetUserDTO
    {
        $user = $this->userRepository->findOneById(new UserUUID($userDTO->id));

        $user->setName(new Name($userDTO->name));
        $userIdentity = $user->getIdentityUser();
        $userIdentity->setPassword($userDTO->password);

        $this->userRepository->save($user);

        return GetUserDTO::createFromUser($user);
    }

    public function removeUser(UserUUID $userId): int
    {
        return $this->userRepository->remove($userId);
    }

    public function getNews(NewsUUID $newsId): GetNewsDTO
    {
        $news = $this->newsRepository->findOneById($newsId);

        return GetNewsDTO::createFromNews($news);
    }

    /**
     * @return News[]|array
     */
    public function getAllNewsWithUsersLikes(): array
    {
        return $this->newsRepository->findAllNewsWithUsersLikes();
    }

    public function addNews(AddNewsDTO $newsDTO): GetNewsDTO
    {
        $newNews = new News(
            new Title($newsDTO->title),
            new Content($newsDTO->content)
        );

        $this->newsRepository->save($newNews);

        return GetNewsDTO::createFromNews($newNews);
    }

    public function editNews(EditNewsDTO $newsDTO): GetNewsDTO
    {
        $news = $this->newsRepository->findOneById(new NewsUUID($newsDTO->id));

        $news->setTitle(new Title($newsDTO->title));
        $news->setContent(new Content($newsDTO->content));

        $this->newsRepository->save($news);

        return GetNewsDTO::createFromNews($news);
    }

    public function removeNews(NewsUUID $newsId): int
    {
        return $this->newsRepository->remove($newsId);
    }

    public function toggleUserLike(UserLikeDTO $userLikeDTO): bool
    {
        $user = $this->userRepository->findOneById(new UserUUID($userLikeDTO->userId));
        if ($user === null) {
            throw new DomainException('Пользователь с ID "' . $userLikeDTO->userId . '" не найден!');
        }

        $news = $this->newsRepository->findOneById(new NewsUUID($userLikeDTO->newsId));
        if ($news === null) {
            throw new DomainException('Новость с ID "' . $userLikeDTO->newsId . '" не найдена!');
        }

        $isNewsAlreadyLiked = $this->newsRepository->isNewsAlreadyLikedByUser(
            $userLikeDTO->newsId,
            $userLikeDTO->userId
        );

        if ($isNewsAlreadyLiked) {
            $user->removeLikedNews($news);
        } else {
            $user->addLikedNews($news);
        }

        $this->userRepository->save($user);

        return !$isNewsAlreadyLiked;
    }

    private function updateIdentityUserPassword(
        IdentityUserInterface $identityUser,
        string $plainPassword
    ): void {
        $identityUser->setPassword(
            $this->passwordHasher->hashPassword($identityUser, $plainPassword)
        );
    }
}
