<?php

namespace App\Repository;

use App\Entity\Timeline;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Timeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timeline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timeline[]    findAll()
 * @method Timeline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Timeline::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Timeline $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Timeline $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Returns a pagination of Timeline.
     */
    public function findPaginated(int $page = 1, int $limit = 10): PaginationInterface
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.title', 'ASC');

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }

    public function findTimelineEventsBySlug(string $slug): ?Timeline
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')->addSelect('c')
            ->leftJoin('t.portals', 'p')->addSelect('p')
            ->leftJoin('t.events', 'e')->addSelect('e')
            ->orderBy('e.timelineOrder')
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
