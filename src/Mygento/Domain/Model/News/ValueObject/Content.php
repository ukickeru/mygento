<?php

namespace App\Mygento\Domain\Model\News\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Embeddable
 */
final class Content
{

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $value;

    /**
     * @throws DomainException
     */
    public function __construct(string $content)
    {
        if (self::isEmpty($content)) {
            throw new DomainException('News\'s content can not be empty!');
        }

        $this->value = $content;
    }

    protected static function isEmpty(string $content): bool
    {
        return strlen($content) === 0;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
