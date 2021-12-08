<?php

namespace App\Mygento\Domain\Model\User\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Embeddable
 */
final class Name
{

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $value;

    /**
     * @throws DomainException
     */
    public function __construct(string $name)
    {
        if (self::isContainsOnlyAlphabetCharacters($name)) {
            throw new DomainException('User\'s name must contain only alphabet symbols!');
        }

        $this->value = $name;
    }

    protected static function isContainsOnlyAlphabetCharacters(string $name): bool
    {
        return mb_ereg_match('^[a-zа-яёЁ]+$', $name, 'i') === false;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
