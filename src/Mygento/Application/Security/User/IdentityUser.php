<?php

namespace App\Mygento\Application\Security\User;

use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;
use App\Mygento\Domain\Model\User\User as DomainUser;
use App\Mygento\Infrastructure\Repository\Doctrine\User\IdentityUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=IdentityUserRepository::class)
 */
class IdentityUser implements UserInterface, PasswordAuthenticatedUserInterface, IdentityUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator("doctrine.uuid_generator")
     */
    private ?string $id = null;

    public const GUARANTEED_USER_ROLE = 'ROLE_USER';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [
        self::GUARANTEED_USER_ROLE
    ];

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $password = '';

    /**
     * @ORM\OneToOne(
     *      targetEntity=DomainUser::class,
     *      inversedBy="identityUser",
     *      fetch="EAGER",
     *      cascade={"remove"},
     *      orphanRemoval=true
     *  )
     * @ORM\JoinColumn(name="domain_user_id_value", referencedColumnName="id_value", nullable=false)
     */
    private DomainUser $domainUser;

    public function __construct(
        string $plainPassword,
        DomainUser $domainUser,
        array $roles = []
    ) {
        $this->password = $plainPassword;
        $this->setDomainUser($domainUser);
        $this->setRoles($roles);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = self::GUARANTEED_USER_ROLE;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $roles[] = self::GUARANTEED_USER_ROLE;

        $this->roles = array_unique($roles);

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $plainPassword): self
    {
        $this->password = $plainPassword;

        return $this;
    }

    public function getDomainUser(): DomainUser
    {
        return $this->domainUser;
    }

    public function getDomainUserId(): ?string
    {
        return $this->domainUser === null ? null : $this->domainUser->getId();
    }

    public function getDomainUserName(): ?string
    {
        return $this->domainUser === null ? null : $this->domainUser->getName();
    }

    private function setDomainUser(DomainUser $user): self
    {
        $this->domainUser = $user;

        $user->setIdentityUser($this);

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDomainUserName();
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }
}
