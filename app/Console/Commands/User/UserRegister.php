<?php

namespace App\Console\Commands\User;

use App\DTO\UserRegisterOldDTO;
use App\Services\UserService;
use Illuminate\Console\Command;

final class UserRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register {name} {email} {--admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register new user and send email';

    private $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dto = new UserRegisterOldDTO($this->argument('name'), $this->argument('email'), $this->option('admin'));

        $user = $this->userService->register($dto);

        return $user->id;
    }
}
