<?php

declare(strict_types=1);

namespace App\Storage;

use RuntimeException;
use function array_filter;
use function array_values;
use function file_exists;
use function file_put_contents;
use function rtrim;
use function var_export;

/**
 * @phpstan-import-type User  from StorageInterface
 * @phpstan-import-type Token from StorageInterface
 * @phpstan-import-type Store from StorageInterface
 */
final class FileStorage implements StorageInterface
{
    private string $path;

    public function __construct(string $dataDir)
    {
        $this->path = rtrim($dataDir, '/') . '/store.php';
        $this->init();
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    /** @return User|null */
    public function findUserByUsername(string $username): ?array
    {
        foreach ($this->read()['users'] as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }

        return null;
    }

    public function createUser(string $uuid, string $username, string $passwordHash): void
    {
        $data            = $this->read();
        $data['users'][] = ['uuid' => $uuid, 'username' => $username, 'password' => $passwordHash];
        $this->write($data);
    }

    // ── Tokens ────────────────────────────────────────────────────────────────

    /** @return Token|null */
    public function findToken(string $accessToken, string $clientToken): ?array
    {
        foreach ($this->read()['tokens'] as $token) {
            if ($token['accessToken'] === $accessToken && $token['clientToken'] === $clientToken) {
                return $token;
            }
        }

        return null;
    }

    public function createToken(
        string $accessToken,
        string $clientToken,
        string $uuid,
        string $username,
    ): void {
        $data             = $this->read();
        $data['tokens'][] = [
            'accessToken' => $accessToken,
            'clientToken' => $clientToken,
            'uuid'        => $uuid,
            'username'    => $username,
        ];
        $this->write($data);
    }

    public function refreshToken(string $uuid, string $newAccessToken): void
    {
        $data = $this->read();

        foreach ($data['tokens'] as &$token) {
            if ($token['uuid'] === $uuid) {
                $token['accessToken'] = $newAccessToken;
                break;
            }
        }

        $this->write($data);
    }

    public function deleteToken(string $accessToken, string $clientToken): void
    {
        $data           = $this->read();
        $data['tokens'] = array_values(array_filter(
            $data['tokens'],
            static fn(array $t): bool =>
                !($t['accessToken'] === $accessToken && $t['clientToken'] === $clientToken),
        ));
        $this->write($data);
    }

    public function deleteTokensByUuid(string $uuid): void
    {
        $data           = $this->read();
        $data['tokens'] = array_values(
            array_filter($data['tokens'], static fn(array $t): bool => $t['uuid'] !== $uuid),
        );
        $this->write($data);
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function init(): void
    {
        if (!file_exists($this->path)) {
            $this->write(['users' => [], 'tokens' => []]);
        }
    }

    /** @return Store */
    private function read(): array
    {
        /** @var Store */
        return require $this->path;
    }

    /** @param Store $data */
    private function write(array $data): void
    {
        $exported = var_export($data, true);

        $open = <<<'NOWDOC'
        <?php return
        NOWDOC;

        file_put_contents($this->path, trim($open) . ' ' . $exported . ';', LOCK_EX);
    }
}
