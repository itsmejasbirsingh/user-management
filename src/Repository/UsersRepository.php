<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Users::class);
    }

public function findAllGreaterThanExp($exp): array
{
    $qb = $this->createQueryBuilder('u')
            ->andWhere('u.exp > :exp')
            ->setParameter('exp', $exp)
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $qb->execute();
}

public function showUsersByAttrs($search): array
{
    //$max = Users::NUMBER_OF_ITEMS;

    $conn = $this->getEntityManager()->getConnection();

      $sql = 'SELECT * FROM users u';

if(trim($search))
{
$sql .= ' WHERE u.username LIKE "%'.$search.'%" or u.email LIKE "%'.$search.'%" or u.about LIKE "%'.$search.'%"';
}

$sql .= ' ORDER BY u.id DESC';


        

     

    $stmt = $conn->prepare($sql);
    $stmt->execute();

  

  

return $stmt->fetchAll();

}


public function findByEmail($email): array
{

    $conn = $this->getEntityManager()->getConnection();

    $sql = "select * from users where email = '".$email."' limit 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}

//    /**
//     * @return Users[] Returns an array of Users objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
