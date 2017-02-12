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
     * @param array $ids
     *
     * @return \Generator
     */
    public function getReferences(array $ids)
    {
        foreach ($ids as $id) {
            yield $this->getReference($id);
        }
    }

    /**
     * @param UuidInterface $id
     *
     * @throws EntityNotFoundException
     *
     * @return mixed
     */
    public function get(UuidInterface $id)
    {
        if (null === $entity = $this->find($id)) {
            throw new EntityNotFoundException();
        }

        return $entity;
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
     * @param $entity
     *
     * @throws \InvalidArgumentException
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
     *
     * @return Pagerfanta
     *
     * @internal param int $pageSize
     * @internal param int $pageIndex
     */
    protected function paginate(QueryBuilder $qb, $fetchJoinCollection = true, $useOutputWalkers = null): Pagerfanta
    {
        return new Pagerfanta(new DoctrineORMAdapter($qb, $fetchJoinCollection, $useOutputWalkers));
    }

    /**
     * @param       $displayField
     * @param array $orderBy
     *
     * @return array
     */
    public function findAllForChoices($displayField, array $orderBy = [])
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id', 'e.'.$displayField);

        foreach ($orderBy as $key => $value) {
            $qb->addOrderBy('e.'.$key, $value);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findLatest(int $page): Pagerfanta
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC');

        return $this->paginate($qb)
            ->setMaxPerPage(constant($this->getClass().'::NUM_ITEMS'))
            ->setCurrentPage($page);
    }
}
