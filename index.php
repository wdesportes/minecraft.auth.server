<?php

declare(strict_types=1);

use App\Controller\AuthenticateController;
use App\Controller\IndexController;
use App\Controller\InvalidateController;
use App\Controller\NotFoundController;
use App\Controller\RefreshController;
use App\Controller\RegisterController;
use App\Controller\SignoutController;
use App\Storage\FileStorage;
use function parse_url;

// ── Autoload ──────────────────────────────────────────────────────────────────

require_once __DIR__ . '/storage/StorageInterface.php';
require_once __DIR__ . '/storage/FileStorage.php';
require_once __DIR__ . '/src/Helpers.php';
require_once __DIR__ . '/src/Controller/Controller.php';
require_once __DIR__ . '/src/Controller/IndexController.php';
require_once __DIR__ . '/src/Controller/AuthenticateController.php';
require_once __DIR__ . '/src/Controller/RefreshController.php';
require_once __DIR__ . '/src/Controller/InvalidateController.php';
require_once __DIR__ . '/src/Controller/SignoutController.php';
require_once __DIR__ . '/src/Controller/RegisterController.php';
require_once __DIR__ . '/src/Controller/NotFoundController.php';

// ── Config ────────────────────────────────────────────────────────────────────

const DATA_DIR = __DIR__ . '/storage';

// ── Bootstrap ─────────────────────────────────────────────────────────────────

$storage = new FileStorage(DATA_DIR);
$path    = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);

// ── Router ────────────────────────────────────────────────────────────────────

$controller = match ($path) {
    '/',         '/index.php'    => new IndexController(),
    '/register', '/register.php' => new RegisterController($storage),

    '/authenticate', '/authenticate.php' => new AuthenticateController($storage),
    '/refresh',      '/refresh.php'      => new RefreshController($storage),
    '/invalidate',   '/invalidate.php'   => new InvalidateController($storage),
    '/signout',      '/signout.php'      => new SignoutController($storage),

    default => new NotFoundController($path),
};

$controller->handle();
