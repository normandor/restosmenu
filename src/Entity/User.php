<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", unique=true)
     */
    private $auth_token;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $logout_token;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string The avatar's path
     * @ORM\Column(type="string")
     */
    private $avatar_path;

    /**
     * @var int The restaurant id for this user
     * @ORM\Column(type="string")
     */
    private $restaurant_id;

    //public function getId(): ?int
    public function getId(): int
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

    public function getLastname(): string
    {
        return (string) $this->lastname;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return (string) $this->firstname;
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
        //return [$roles];
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
        return $this->password;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function getAvatarPath(): string
    {
        return $this->avatar_path;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->auth_token = $authToken;

        return $this;
    }

    public function setLogoutToken(string $logoutToken): self
    {
        $this->logout_token = $logoutToken;

        return $this;
    }

    public function setAvatarPath(string $path): self
    {
        $this->avatar_path = $path;

        return $this;
    }

    public function setRestaurantId(int $restaurantId): self
    {
        $this->restaurant_id = $restaurantId;

        return $this;
    }

    public function getRestaurantId(): int
    {
        return $this->restaurant_id;
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

}
