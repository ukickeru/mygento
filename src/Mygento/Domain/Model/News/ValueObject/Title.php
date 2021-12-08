<?php

namespace App\Mygento\Domain\Model\News\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Embeddable
 */
final class Title
{

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $value;

    /**
     * @throws DomainException
     */
    public function __construct(string $title)
    {
        if (self::isEmpty($title)) {
            throw new DomainException('News\'s title can not be empty!');
        }

        $this->value = $title;
    }

    protected static function isEmpty(string $title): bool
    {
        return strlen($title) === 0;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
