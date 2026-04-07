<?php

declare(strict_types=1);

namespace App\Controller;

use App\Storage\StorageInterface;

final class InvalidateController extends Controller
{
    public function __construct(private readonly StorageInterface $storage) {}

    public function handle(): never
    {
        $body        = $this->jsonInput();
        $accessToken = $this->requireField($body, 'accessToken');
        $clientToken = $this->requireField($body, 'clientToken');

        if ($this->storage->findToken($accessToken, $clientToken) === null) {
            $this->forbidden('Invalid token.');
        }

        $this->storage->deleteToken($accessToken, $clientToken);

        $this->noContent();
    }
}
