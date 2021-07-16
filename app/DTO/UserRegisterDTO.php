<?php

namespace App\DTO;

class UserRegisterDTO
{
    public string $email;
    public string $name;
    public array $role;

    public function __construct(array $attributes)
    {
        $this->email = $attributes['email'];
        $this->name = $attributes['name'];
        $this->role = $attributes['role'];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
