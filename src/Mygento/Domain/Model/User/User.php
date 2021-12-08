<?php

namespace App\Mygento\Domain\Model\User;

use App\Mygento\Application\Security\User\IdentityUser;
use App\Mygento\Domain\Model\News\News;
use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;
use App\Mygento\Domain\Model\User\ValueObject\Name;
use App\Mygento\Domain\Model\User\ValueObject\UUID;
use App\Mygento\Infrastructure\Repository\Doctrine\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Embedded(class=UUID::class)
     */
    private ?UUID $id = null;

    /**
     * @ORM\Embedded(class=Name::class)
     */
    private Name $name;

    /**
     * @ORM\OneToOne(
     *      targetEntity=IdentityUser::class,
     *      mappedBy="domainUser",
     *      fetch="EAGER",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     *  )
     */
    private ?IdentityUserInterface $identityUser;

    /**
     * @ORM\ManyToMany(targetEntity=News::class, mappedBy="likedUsers", fetch="LAZY")
     */
    private Collection $likedNews;

    public function __construct(
        Name $name,
        IdentityUserInterface $identityUser = null,
        array $likedNews = []
    ) {
        $this->name = $name;
        $this->identityUser = $identityUser;
        $this->likedNews = new ArrayCollection();
        $this->addFewLikedNews($likedNews);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(Name $name): self
    {
        $this->name = $name;

        return  $this;
    }

    public function getIdentityUser(): ?IdentityUserInterface
    {
        return $this->identityUser;
    }

    public function getLikedNews(): array
    {
        return $this->likedNews->toArray();
    }

    public function addFewLikedNews(array $newsArray): self
    {
        foreach ($newsArray as $news) {
            $this->addLikedNews($news);
        }

        return $this;
    }

    public function addLikedNews(News $news): self
    {
        if (!$this->likedNews->contains($news)) {
            $this->likedNews[] = $news;
            $news->addLikedUser($this);
        }

        return $this;
    }

    public function removeLikedNews(News $news): self
    {
        if ($this->likedNews->contains($news)) {
            $this->likedNews->removeElement($news);
            $news->removeLikedUser($this);
        }

        return $this;
    }

    public function setIdentityUser(IdentityUserInterface $identityUser): self
    {
        $this->identityUser = $identityUser;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
