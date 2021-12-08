<?php

namespace App\Tests\Mygento\Infrastructure\Repository\Doctrine\User;

use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use App\Mygento\Domain\UseCase\User\UserRepositoryInterface;
use App\Mygento\Infrastructure\Repository\Doctrine\User\UserRepository;
use App\Tests\Mygento\Infrastructure\Repository\Doctrine\DoctrineRepositoryTestCase;

/**
 * @covers UserRepository
 */
class UserRepositoryTest extends DoctrineRepositoryTestCase
{
    use CreateUserTrait;

    protected UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @covers UserRepository::findOneById
     * @covers UserRepository::save
     */
    public function testSave()
    {
        $this->entityManager->clear();

        $identityUser = self::createIdentityUser();
        $user = $identityUser->getDomainUser();

        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());
    }

    /**
     * @covers UserRepository::findOneById
     * @covers UserRepository::update
     */
    public function testUpdate()
    {
        $this->entityManager->clear();

        $identityUser = self::createIdentityUser();
        $user = $identityUser->getDomainUser();

        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());

        $userId = (string) $user->getId();

        $user->setName(new Name('UpdatedUsername'));
        $identityUser->setPassword('UpdatedPassword');

        $this->userRepository->update($user);

        unset($user, $identityUser);

        $this->entityManager->clear();

        $userFromRepository = $this->userRepository->findOneById($userId);

        $this->assertEquals('UpdatedUsername', $userFromRepository->getName());
        $this->assertEquals('UpdatedPassword', $userFromRepository->getIdentityUser()->getPassword());
    }

    /**
     * @covers UserRepository::findOneById
     * @covers UserRepository::remove
     */
    public function testRemove()
    {
        $this->entityManager->clear();

        $identityUser = self::createIdentityUser();
        $user = $identityUser->getDomainUser();

        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());

        $userId = (string) $user->getId();

        $deletedUserCount = $this->userRepository->remove($user->getId());

        $this->assertSame(1, $deletedUserCount);

        $userFromRepository = $this->userRepository->findOneById($userId);

        $this->assertNull($userFromRepository);
    }
}
