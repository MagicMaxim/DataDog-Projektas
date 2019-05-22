<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Controller\EventController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min=6,
     *     minMessage = "Password must atleast be {{ limit }} of length"
     * )
     */
    private $newPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="Event")
     */
    private $usersEvents;
    /**
     * @ORM\Column(name="passwordResetToken", type="string", length=255, nullable=true)
     * @Assert\Type("string")
     */
    private $passwordResetToken;
    /**
     * @ORM\ManyToMany(targetEntity="Category")
     */
    private $usersCategories;

    public function __construct()
    {
        $this->usersEvents = new ArrayCollection();
        $this->usersCategories = new ArrayCollection();

    }
    /**
     * @return null|string
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }
    /**
     * @param null|string $passwordResetTokenProfile
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
    }
    public function addCategoryToUser(Category $category){
        $this->usersCategories[] = $category;
    }

    public function removeCategoryfromUser(Category $category){
        $this->usersCategories->removeElement($category);
    }

    public function getAllUserCategories()
    {
        return $this->usersCategories;
    }

     public function addEventToUser(Event $event){
        $this->usersEvents[] = $event;
     }

    public function removeEventfromUser(Event $event){
        $this->usersEvents->removeElement($event);
    }

     public function getAllUserEvents()
     {
         return $this->usersEvents;
     }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }
    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword)
    {
        $this->newPassword = $newPassword;
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
}
