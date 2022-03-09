<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Data\SearchData;
use App\Entity\User;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Article::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Article $entity, bool $flush = true): void
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
    public function remove(Article $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findLastArticles(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Portal[] $portals
     * 
     * @return Articles[]
     */
    public function findByPortals(array $portals, int $page, int $limit = 10): PaginationInterface
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->leftJoin('a.portals', 'p')->addSelect()
            ->andWhere('p.id IN (:portals)')
            ->setParameter('portals', $portals)
        ;

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }
    
    /**
     * Finds a pagination of 16 articles.
     * 
     * @param int $page
     * 
     * @return PaginationInterface
     */
    public function findPaginated(int $page): PaginationInterface
    {
        $builder =  $this->createQueryBuilder('a')->orderBy('a.title', 'ASC');
        return $this->paginatorService->setLimit(16)->paginate($builder, $page);
    }

    /**
     * Finds an article and its portals.
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->getDefaultQueryBuilder()
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByUser(User $user, int $page): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder()
            ->andWhere('a.author = :user')
            ->setParameter('user', $user)
        ;

        return $this->paginatorService->setLimit(12)->paginate($builder, $page);
    }

    public function search(SearchData $search, int $limit = 10): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder();

        if (strlen($search->getQuery()) >= 3 AND $search->getQuery() !== null) {
            $builder
                ->andWhere('a.title LIKE :q_1')
                ->setParameter('q_1', '%' . $search->getQuery() . '%')
                ->orWhere('a.description LIKE :q_2')
                ->setParameter('q_2', '%' . $search->getQuery() . '%')
                ->orWhere('a.content LIKE :q_3')
                ->setParameter('q_3', '%' . $search->getQuery() . '%')
            ;
        }
        
        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('p.id IN (:portals)')
                ->setParameter('portals', $search->getPortals())
            ;
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $search->getPage());
    }

    private function getDefaultQueryBuilder(): QueryBuilder
    {
        return  $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->leftJoin('a.portals', 'p')->addSelect('p')
            ->leftJoin('a.author', 'u')->addSelect('u')
        ;
    }
}
