<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Apartment", mappedBy="owner", orphanRemoval=true)
     */
    private $apartments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user_id", cascade={"persist", "remove"})
     */
    private $userInfo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bookmark", mappedBy="user", orphanRemoval=true)
     */
    private $bookmarks;


    public function __construct()
    {
        $this->apartments = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    



    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Apartment[]
     */
    public function getApartments(): Collection
    {
        return $this->apartments;
    }

    public function addApartment(Apartment $apartment): self
    {
        if (!$this->apartments->contains($apartment)) {
            $this->apartments[] = $apartment;
            $apartment->setOwner($this);
        }

        return $this;
    }

    public function removeApartment(Apartment $apartment): self
    {
        if ($this->apartments->contains($apartment)) {
            $this->apartments->removeElement($apartment);
            // set the owning side to null (unless already changed)
            if ($apartment->getOwner() === $this) {
                $apartment->setOwner(null);
            }
        }

        return $this;
    }    



    public function getUserInfo(): ?UserInfo
    {
        return $this->userInfo;
    }

    public function setUserInfo(UserInfo $userInfo): self
    {
        $this->userInfo = $userInfo;

        // set the owning side of the relation if necessary
        if ($this !== $userInfo->getUserId()) {
            $userInfo->setUserId($this);
        }

        return $this;
    }

    /**
     * @return Collection|Bookmark[]
     */
    public function getBookmarks(): Collection
    {
        return $this->bookmarks;
    }

    public function addBookmark(Bookmark $bookmark): self
    {
        if (!$this->bookmarks->contains($bookmark)) {
            $this->bookmarks[] = $bookmark;
            $bookmark->setUser($this);
        }

        return $this;
    }

    public function removeBookmark(Bookmark $bookmark): self
    {
        if ($this->bookmarks->contains($bookmark)) {
            $this->bookmarks->removeElement($bookmark);
            // set the owning side to null (unless already changed)
            if ($bookmark->getUser() === $this) {
                $bookmark->setUser(null);
            }
        }

        return $this;
    }

}
