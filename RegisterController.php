<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers;
use App\Storage\StorageInterface;
use function header;
use function htmlspecialchars;
use function preg_match;
use function trim;

final class RegisterController extends Controller
{
    public function __construct(private readonly StorageInterface $storage) {}

    public function handle(): never
    {
        $error   = null;
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = trim((string) ($_POST['password'] ?? ''));

            if ($username === '' || $password === '') {
                $error = 'Username and password are required.';
            } elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
                $error = 'Username may only contain letters, digits, and underscores.';
            } elseif ($this->storage->findUserByUsername($username) !== null) {
                $error = 'That username is already taken.';
            } else {
                $this->storage->createUser(
                    Helpers::genUuid(),
                    $username,
                    Helpers::hashPassword($password),
                );
                $success = true;
            }
        }

        $this->renderHtml($error, $success);
    }

    private function renderHtml(?string $error, bool $success): never
    {
        header('Content-Type: text/html; charset=UTF-8');

        if ($success) {
            $this->renderSuccess();
        }

        $this->renderForm($error);
    }

    private function renderSuccess(): never
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Register</title>
        </head>
        <body>
            <h1>Create account</h1>
            <p class="success">Account created successfully.</p>
        </body>
        </html>
        HTML;

        exit;
    }

    private function renderForm(?string $error): never
    {
        $errorHtml = $error !== null
            ? '<p class="error">' . htmlspecialchars($error) . '</p>'
            : '';

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Register</title>
            <style>
                body { font-family: sans-serif; max-width: 400px; margin: 4rem auto; }
                label { display: block; margin-top: 1rem; }
                input[type=text], input[type=password] { width: 100%; padding: .4rem; }
                button { margin-top: 1.2rem; padding: .5rem 1.2rem; }
                .error { color: red; }
                .success { color: green; }
            </style>
        </head>
        <body>
            <h1>Create account</h1>
            {$errorHtml}
            <form method="post" action="/register.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="16" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" maxlength="72" required>
                <button type="submit">Register</button>
            </form>
        </body>
        </html>
        HTML;

        exit;
    }
}
