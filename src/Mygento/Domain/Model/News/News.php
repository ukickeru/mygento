<?php

namespace App\Mygento\Domain\Model\News;

use App\Mygento\Domain\Model\News\ValueObject\UUID;
use App\Mygento\Domain\Model\News\ValueObject\Title;
use App\Mygento\Domain\Model\News\ValueObject\Content;
use App\Mygento\Domain\Model\User\User;
use App\Mygento\Infrastructure\Repository\Doctrine\News\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class News implements NewsInterface
{
    /**
     * @ORM\Embedded(class=UUID::class)
     */
    private ?UUID $id = null;

    /**
     * @ORM\Embedded(class=Title::class)
     */
    private Title $title;

    /**
     * @ORM\Embedded(class=Content::class)
     */
    private Content $content;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="likedNews", fetch="LAZY")
     * @ORM\JoinTable(name="users_liked_news",
     *      joinColumns={
     *          @ORM\JoinColumn(name="news_id", referencedColumnName="id_value")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id_value")
     *      }
     *  )
     */
    private Collection $likedUsers;

    public function __construct(
        Title $title,
        Content $content,
        array $likedUsers = []
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->likedUsers = new ArrayCollection();
        $this->addFewLikedUsers($likedUsers);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(Title $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLikedUsers(): array
    {
        return $this->likedUsers->toArray();
    }

    public function addFewLikedUsers(array $userArray): self
    {
        foreach ($userArray as $user) {
            $this->addLikedUser($user);
        }

        return $this;
    }

    public function addLikedUser(User $user): self
    {
        if (!$this->likedUsers->contains($user)) {
            $this->likedUsers[] = $user;
            $user->addLikedNews($this);
        }

        return $this;
    }

    public function removeLikedUser(User $user): self
    {
        if ($this->likedUsers->contains($user)) {
            $this->likedUsers->removeElement($user);
            $user->removeLikedNews($this);
        }

        return $this;
    }
}
