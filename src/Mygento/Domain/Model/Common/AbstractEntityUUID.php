<?php

namespace App\Mygento\Domain\Model\Common;

use LogicException;

abstract class AbstractEntityUUID
{
    protected ?string $value = null;

    /**
     * @throws LogicException
     */
    public function __construct(string $id)
    {
        if (self::isNotInUUID4Format($id)) {
            throw new LogicException('UUID must be in a UUID format!');
        }

        $this->value = $id;
    }

    protected static function isNotInUUID4Format(string $id): bool
    {
        return mb_ereg_match(
            '^[a-z0-9]{8,8}-[a-z0-9]{4,4}-[a-z0-9]{4,4}-[a-z0-9]{4,4}-[a-z0-9]{12,12}$',
            $id,
            'i'
        ) === false;
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
