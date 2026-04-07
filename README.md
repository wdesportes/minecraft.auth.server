# wdes.auth.server

Yggdrasil-compatible authentication server. PHP 8.4, strict types, PSR-12, front-controller pattern.

## Setup

```bash
chmod 750 storage/
```

`storage/` must be writable by the web server. It is git-ignored by default.  
Visit `/register` to create the first account.

## Endpoints

| Method | Path                      | Body fields                            |
|--------|---------------------------|----------------------------------------|
| GET    | /                         | —                                      |
| POST   | /authenticate             | `username`, `password`, `clientToken`? |
| POST   | /refresh                  | `accessToken`, `clientToken`           |
| POST   | /invalidate               | `accessToken`, `clientToken`           |
| POST   | /signout                  | `username`, `password`                 |
| GET/POST | /register               | form: `username`, `password`           |

## Swapping the storage backend

Implement `StorageInterface`, then change one line in `index.php`:

```php
$storage = new PdoStorage($pdo);
```
