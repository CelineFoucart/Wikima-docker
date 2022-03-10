<?php

namespace App\Repository;

use App\Entity\Data\SearchData;
use App\Entity\Image;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Image::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Image $entity, bool $flush = true): void
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
    public function remove(Image $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findBySlug(string $slug): ?Image
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('i.categories', 'c')->addSelect('c')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findPaginated(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')->orderBy('i.title', 'ASC');

        return $this->paginatorService->paginate($builder, $page);
    }

    public function search(SearchData $search): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')
            ->orderBy('i.title', 'ASC')
            ->leftJoin('i.portals', 'p')->addSelect('p')
        ;

        if (strlen($search->getQuery()) >= 3 AND $search->getQuery() !== null) {
            $builder
                ->andWhere('i.title LIKE :q_1')
                ->setParameter('q_1', '%' . $search->getQuery() . '%')
                ->orWhere('i.description LIKE :q_2')
                ->setParameter('q_2', '%' . $search->getQuery() . '%')
                ->orWhere('i.keywords LIKE :q_3')
                ->setParameter('q_3', '%' . $search->getQuery() . '%')
            ;
        }

        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('p.id IN (:portals)')
                ->setParameter('portals', $search->getPortals())
            ;
        }

        return $this->paginatorService->paginate($builder, $search->getPage());
    }
}
