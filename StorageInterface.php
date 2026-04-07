<?php

declare(strict_types=1);

namespace App\Storage;

/**
 * @phpstan-type User  array{uuid: string, username: string, password: string}
 * @phpstan-type Token array{accessToken: string, clientToken: string, uuid: string, username: string}
 * @phpstan-type Store array{users: list<User>, tokens: list<Token>}
 */
interface StorageInterface
{
    /** @return User|null */
    public function findUserByUsername(string $username): ?array;

    public function createUser(string $uuid, string $username, string $passwordHash): void;

    /** @return Token|null */
    public function findToken(string $accessToken, string $clientToken): ?array;

    public function createToken(
        string $accessToken,
        string $clientToken,
        string $uuid,
        string $username,
    ): void;

    public function refreshToken(string $uuid, string $newAccessToken): void;

    public function deleteToken(string $accessToken, string $clientToken): void;

    public function deleteTokensByUuid(string $uuid): void;
}
