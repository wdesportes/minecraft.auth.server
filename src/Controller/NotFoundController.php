<?php

declare(strict_types=1);

namespace App\Controller;

final class NotFoundController extends Controller
{
    public function __construct(private readonly ?string $path) {}

    public function handle(): never
    {
        $this->error(
            'NotFound',
            'The server has not found anything matching the request URI: ' . $this->path,
            404,
        );
    }
}
