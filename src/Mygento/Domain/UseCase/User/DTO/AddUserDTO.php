<?php

namespace App\Mygento\Domain\UseCase\User\DTO;

final class AddUserDTO
{
    public string $name = '';

    public string $plainPassword = '';
}