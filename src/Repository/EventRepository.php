<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use App\Repository\DateTime;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllWithCategories()
    {
        return $this->createQueryBuilder('e')
            ->join('e.category', 'c')
            ->addSelect('c')->getQuery()
            ->getResult();
    }

    public function getEventsByCriteria(?string $title, ?string $description, ?string $price, ?string $location): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');
        if($title){
            $qb->andWhere($qb->expr()->like('e.title', ':title'))
                ->setParameter('title', '%'. $title. '%');
        }
        if($description){
            $qb->andWhere($qb->expr()->like('e.description', ':description'))
                ->setParameter('description', '%'. $description. '%');
        }
        
        if($price || $price == 0){
            $price = strpos($price, '.') ?  $price : $price . ".00";
            $qb->andWhere($qb->expr()->like('e.price', ':price'))
                ->setParameter('price', $price);
        }
        if($location){
            $qb->andWhere($qb->expr()->like('e.location', ':location'))
                ->setParameter('location', '%'. $location. '%');
        }
        return $qb;
    }
    public function getWithSearchQueryBuilder(): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        return  $qb->select('e')->from($this->_entityName, 'e');
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
