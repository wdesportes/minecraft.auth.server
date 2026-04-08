<?php

declare(strict_types=1);

namespace App;

use function bin2hex;
use function password_hash;
use function password_verify;
use function random_bytes;

final class Helpers
{
    private function __construct() {}

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function genUuid(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function genAccessToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function genClientToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
