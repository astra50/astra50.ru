<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Generator;
use InvalidArgumentException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class EntityRepository
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getReference(int $id): ?object
    {
        return $this->em->getReference($this->getClass(), $id);
    }

    public function getReferences(array $ids): Generator
    {
        foreach ($ids as $id) {
            yield $this->getReference($id);
        }
    }

    public function get(int $id): object
    {
        if (null === $entity = $this->find($id)) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    public function find(int $id)
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(object $entity): void
    {
        $class = $this->getClass();

        if (!$entity instanceof $class) {
            throw new InvalidArgumentException(
                sprintf('Entity %s is not related to this repository', ClassUtils::getClass($entity))
            );
        }

        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    public function createQueryBuilder(string $alias): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from($this->getClass(), $alias);
    }

    public function findAllForChoices(string $displayField, array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id', 'e.'.$displayField);

        foreach ($orderBy as $key => $value) {
            $qb->addOrderBy('e.'.$key, $value);
        }

        return $qb->getQuery()->getResult();
    }

    public function findLatest(int $page): Pagerfanta
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->getQuery();

        return $this->createPaginator($query, constant($this->getClass().'::NUM_ITEMS'), $page);
    }

    /**
     * @return string Entity Class
     */
    abstract protected function getClass(): string;

    protected function createPaginator(Query $query, int $pageSize, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, false));
        $paginator->setMaxPerPage($pageSize);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
