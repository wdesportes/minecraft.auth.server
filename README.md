# wdes.auth.server

Yggdrasil-compatible authentication server. PHP 8.4, strict types, PSR-12, front-controller pattern.

## Setup

```bash
chmod 750 storage/
```

The `storage/` folder must be writable by the web server. It is git-ignored by default.

Visit `/register.php` to create your account.

## Endpoints

| Method   | Path                                   | Body fields                            |
|----------|----------------------------------------|----------------------------------------|
| GET      | `/` or `/index.php`                    | n/a                                    |
| POST     | `/authenticate` or `/authenticate.php` | `username`, `password`, `clientToken`? |
| POST     | `/refresh` or `/refresh.php`            | `accessToken`, `clientToken`           |
| POST     | `/invalidate` or `/invalidate.php`      | `accessToken`, `clientToken`           |
| POST     | `/signout` or `/signout.php`           | `username`, `password`                 |
| GET/POST | `/register` or `/register.php`         | form: `username`, `password`           |

## Swapping the storage backend

Implement `StorageInterface`, then change one line in `index.php`:

```php
$storage = new PdoStorage($pdo);
```
