<?php

namespace App\Mygento\Infrastructure\Repository\Doctrine\User;

use App\Mygento\Application\Security\User\IdentityUser;
use App\Mygento\Domain\Model\User\User as DomainUser;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method IdentityUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdentityUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdentityUser[]    findAll()
 * @method IdentityUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentityUserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdentityUser::class);
    }

    public function getIdentityUserIdByDomainUsername(Name $username): ?string
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb->select('iu.id')
                ->from(IdentityUser::class, 'iu')
                ->innerJoin(DomainUser::class, 'du', Join::WITH, 'iu.domainUser = du.id.value')
                ->where('du.name.value = :username')
                ->setParameter('username', (string) $username)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof IdentityUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
