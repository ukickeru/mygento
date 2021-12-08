<?php

namespace App\Mygento\Infrastructure\Repository\Doctrine\News;

use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Domain\UseCase\News\NewsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findOneById(string $id): ?News
    {
        return $this->find($id);
    }

    public function findAllNewsWithUsersLikes(): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('n')
            ->from(News::class, 'n')
            ->innerJoin(User::class, 'u', Join::WITH)
            ->groupBy('n.id.value')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SIMPLEOBJECT);
    }

    public function isNewsAlreadyLikedByUser(string $newsId, string $userId): bool
    {
        $sql = " 
            SELECT
                count(uln.news_id) as likes_count
            FROM
                users_liked_news AS uln
            WHERE
                user_id = :userId
            AND
                news_id = :newsId
        ";

        $rsm = (new ResultSetMapping())
            ->addScalarResult('likes_count', 'likes_count');

        $result = $this->_em
            ->createNativeQuery($sql, $rsm)
            ->setParameter('userId', $userId)
            ->setParameter('newsId', $newsId)
            ->getSingleScalarResult();

        return $result != 0;
    }

    public function update(News $news): void
    {
        $this->_em->persist($news);
        $this->_em->flush();
    }

    public function save(News $news): void
    {
        $this->_em->persist($news);
        $this->_em->flush();
    }

    public function remove(string $id): int
    {
        $news = $this->findOneById($id);

        if ($news === null) {
            return 0;
        }

        $this->_em->remove($news);
        $this->_em->flush();

        return 1;
    }
}
