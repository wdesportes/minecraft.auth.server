<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers;
use App\Storage\StorageInterface;

final class RefreshController extends Controller
{
    public function __construct(private readonly StorageInterface $storage) {}

    public function handle(): never
    {
        $body        = $this->jsonInput();
        $accessToken = $this->requireField($body, 'accessToken');
        $clientToken = $this->requireField($body, 'clientToken');

        $token = $this->storage->findToken($accessToken, $clientToken);

        if ($token === null) {
            $this->forbidden('Invalid token.');
        }

        $newAccessToken = Helpers::genAccessToken();

        $this->storage->refreshToken($token['uuid'], $newAccessToken);

        $this->json([
            'accessToken'       => $newAccessToken,
            'clientToken'       => $clientToken,
            'selectedProfile'   => ['id' => $token['uuid'], 'name' => $token['username']],
            'availableProfiles' => [['id' => $token['uuid'], 'name' => $token['username']]],
        ]);
    }
}
