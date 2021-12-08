<?php

namespace App\Mygento\Domain\Model\News\ValueObject;

use App\Mygento\Domain\Model\Common\AbstractEntityUUID;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class UUID extends AbstractEntityUUID
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator("doctrine.uuid_generator")
     */
    protected ?string $value = null;
}
