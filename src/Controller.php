<?php

declare(strict_types=1);

namespace App\Controller;

use function file_get_contents;
use function header;
use function http_response_code;
use function json_decode;
use function json_encode;
use function trim;

abstract class Controller
{
    abstract public function handle(): never;

    // ── Response ──────────────────────────────────────────────────────────────

    /** @param array<string, mixed> $data */
    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }

    protected function noContent(): never
    {
        http_response_code(204);
        exit;
    }

    // ── Request ───────────────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    protected function jsonInput(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || $raw === '') {
            $this->error('MethodNotAllowed', 'The method specified in the request is not allowed.', 405);
        }

        /** @var array<string, mixed> */
        return json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    }

    /** @param array<string, mixed> $data */
    protected function requireField(array $data, string $field): string
    {
        $value = isset($data[$field]) ? trim((string) $data[$field]) : '';

        if ($value === '') {
            $this->error('IllegalArgumentException', "Field '{$field}' cannot be null or empty.", 400);
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    protected function optionalField(array $data, string $field): ?string
    {
        $value = isset($data[$field]) ? trim((string) $data[$field]) : '';

        return $value !== '' ? $value : null;
    }

    // ── Errors ────────────────────────────────────────────────────────────────

    protected function error(string $error, string $message, int $status = 400): never
    {
        $this->json(['error' => $error, 'errorMessage' => $message], $status);
    }

    protected function forbidden(string $message): never
    {
        $this->error('ForbiddenOperationException', $message, 403);
    }
}
