<?php

namespace App\Mygento\Infrastructure\Repository\Doctrine\User;

use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\Model\User\ValueObject\UUID;
use App\Mygento\Domain\UseCase\News\DTO\AddUserLikeDTO;
use App\Mygento\Domain\UseCase\User\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneById(string $id): ?User
    {
        return $this->find($id);
    }

    public function update(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function remove(string $id): int
    {
        $user = $this->findOneById($id);

        if ($user === null) {
            return 0;
        }

        $this->_em->remove($user);
        $this->_em->flush();

        return 1;
    }
}
