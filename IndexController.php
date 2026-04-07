<?php

declare(strict_types=1);

namespace App\Controller;

final class IndexController extends Controller
{
    public function handle(): never
    {
        $this->json([
            'Status'                  => 'OK',
            'Runtime-Mode'            => 'productionMode',
            'Application-Description' => 'Yggdrasil-compatible authentication server.',
            'Specification-Version'   => '1.0',
            'Application-Name'        => 'wdes.auth.server',
            'Implementation-Version'  => '1.0',
        ]);
    }
}
