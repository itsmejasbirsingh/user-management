<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationList;


/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public $errors = [];

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


public function validate($input)
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection(array(          
            'username' =>  array( new Assert\Length(array('min' => 3)), new Assert\NotBlank() ),
            'email' =>   array( new Assert\Email() ),
            'exp' => new Assert\GreaterThan(3),     
            'password' => new Assert\NotBlank(),
            'about' => new Assert\NotBlank(),
            'mobile' => null, 
        ));


        $violations = $validator->validate($input, $constraint);

        if (count($violations)) {
            $this->errors = self::violations_to_array($violations);
            return false;
        }

        return true;
    }
  
    private function violations_to_array(ConstraintViolationList $violationsList, $propertyPath = null)
    {
        $output = array();
        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        if (null !== $propertyPath) {
            if (array_key_exists($propertyPath, $output)) {
                $output = array($propertyPath => $output[$propertyPath]);
            } else {
                return array();
            }
        }
        return $output;
    }


}
