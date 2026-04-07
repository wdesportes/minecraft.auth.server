<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers;
use App\Storage\StorageInterface;

final class SignoutController extends Controller
{
    public function __construct(private readonly StorageInterface $storage) {}

    public function handle(): never
    {
        $body     = $this->jsonInput();
        $username = $this->requireField($body, 'username');
        $password = $this->requireField($body, 'password');

        $user = $this->storage->findUserByUsername($username);

        if ($user === null || !Helpers::verifyPassword($password, $user['password'])) {
            $this->forbidden('Invalid credentials. Invalid username or password.');
        }

        $this->storage->deleteTokensByUuid($user['uuid']);

        $this->noContent();
    }
}
