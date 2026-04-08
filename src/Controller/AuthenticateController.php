<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers;
use App\Storage\StorageInterface;

final class AuthenticateController extends Controller
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

        $clientToken = $this->optionalField($body, 'clientToken') ?? Helpers::genClientToken();
        $accessToken = Helpers::genAccessToken();

        $this->storage->createToken($accessToken, $clientToken, $user['uuid'], $user['username']);

        $this->json([
            'accessToken'       => $accessToken,
            'clientToken'       => $clientToken,
            'selectedProfile'   => ['id' => $user['uuid'], 'name' => $user['username']],
            'availableProfiles' => [['id' => $user['uuid'], 'name' => $user['username']]],
        ]);
    }
}
