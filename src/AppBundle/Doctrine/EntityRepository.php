<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <ko@grachev.io>
 */
abstract class EntityRepository
{
    use EntityManagerTrait;

    /**
     * @return string Entity Class
     */
    abstract protected function getClass(): string;

    /**
     * @param UuidInterface $id
     *
     * @return mixed
     */
    public function getReference(UuidInterface $id)
    {
        return $this->em->getReference($this->getClass(), $id);
    }

    /**
     * @param UuidInterface $id
     */
    public function find(UuidInterface $id)
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param UuidInterface $id
     *
     * @return mixed
     *
     * @throws EntityNotFoundException
     */
    public function get(UuidInterface $id)
    {
        if (null === $entity = $this->find($id)) {
            throw new EntityNotFoundException();
        }

        return $entity;
    }

    /**
     * @param $entity
     */
    public function save($entity)
    {
        $class = $this->getClass();

        if (!$entity instanceof $class) {
            throw new \InvalidArgumentException();
        }

        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    /**
     * @param $alias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from($this->getClass(), $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param int          $pageSize
     * @param int          $pageIndex
     *
     * @return Pagerfanta
     */
    protected function paginate(QueryBuilder $qb, int $pageSize, int $pageIndex): Pagerfanta
    {
        return (new Pagerfanta(new DoctrineORMAdapter($qb)))
            ->setMaxPerPage($pageSize)
            ->setCurrentPage($pageIndex);
    }
}
