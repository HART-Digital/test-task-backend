<?php

namespace App\DTO;

class UserRegisterOldDTO
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var bool */
    private $admin;

    /**
     * UserRegisterOldDTO constructor.
     *
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email, bool $admin)
    {
        $this->name = $name;
        $this->email = $email;
        $this->admin = $admin;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }
}
