<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
	private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {

    	$this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setUsername('admin2');
        $user->setEmail('admin@email.com');
        $user->setExp('2');
        $user->setAbout('administrator');
        $user->setPassword(
                  $this->encoder->encodePassword($user, '1111')
        	);

        $manager->persist($user);

        $manager->flush();
    }
}
