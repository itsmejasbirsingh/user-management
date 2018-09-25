<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(fields="email", message="Email already exist")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class Users implements UserInterface, \Serializable
{
	const NUMBER_OF_ITEMS = 5;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=20)
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**     
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)    
     */
    private $about;

    /**   
     * @ORM\Column(type="integer")     
     */
    private $exp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

   

    public function getId(): ?int
    {        
        return $this->id;
    }


    public function getExp(): ?int
        {

            return $this->exp;
        }

    public function setExp(string $exp): self
        {
            $this->exp = $exp;

            return $this;
        }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $name): self
    {
        $this->username = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getRoles()
    {

        return [
            'ROLE_USER'
        ];
    }

    public function getSalt() {}
    public function eraseCredentials() {}
    public function serialize() {

        return serialize([
               $this->id,
               $this->username,
               $this->email,
               $this->password

            ]);
    }

    public function unserialize($string)
    {

        list (
              $this->id,
               $this->username,
               $this->email,
               $this->password
            ) = unserialize($string, ['allowed_classes' => false]);
    }

  
}
