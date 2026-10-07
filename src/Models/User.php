<?php
namespace App\Models;

/**
 * User Model (MVC - Model Katmanı)
 * Mezun ve kullanıcı verilerinin yönetiminden ve kalıcılığından sorumludur.
 */
class User
{
    private static function getStoragePath(): string
    {
        return dirname(__DIR__, 2) . '/data/users.json';
    }

    private static function loadAll(): array
    {
        $file = self::getStoragePath();
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }

    private static function saveAll(array $users): bool
    {
        $file = self::getStoragePath();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return (bool)file_put_contents(
            $file,
            json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public static function all(): array
    {
        return self::loadAll();
    }

    public static function find(int $id): ?array
    {
        $users = self::loadAll();
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    public static function create(array $data): array
    {
        $users = self::loadAll();

        $maxId = 0;
        foreach ($users as $u) {
            if (isset($u['id']) && $u['id'] > $maxId) {
                $maxId = $u['id'];
            }
        }
        $newId = $maxId + 1;

        $newUser = [
            'id'             => $newId,
            'name'           => trim($data['name']),
            'email'          => trim($data['email'] ?? ''),
            'graduationYear' => isset($data['graduationYear']) ? (int)$data['graduationYear'] : null,
            'department'     => trim($data['department'] ?? ''),
            'company'        => trim($data['company'] ?? '')
        ];

        $users[] = $newUser;
        self::saveAll($users);

        return $newUser;
    }

    public static function update(int $id, array $data): ?array
    {
        $users = self::loadAll();
        $targetIndex = -1;

        foreach ($users as $index => $u) {
            if (isset($u['id']) && $u['id'] === $id) {
                $targetIndex = $index;
                break;
            }
        }

        if ($targetIndex === -1) {
            return null;
        }

        $users[$targetIndex] = [
            'id'             => $id,
            'name'           => trim($data['name']),
            'email'          => trim($data['email'] ?? ''),
            'graduationYear' => isset($data['graduationYear']) ? (int)$data['graduationYear'] : null,
            'department'     => trim($data['department'] ?? ''),
            'company'        => trim($data['company'] ?? '')
        ];

        self::saveAll($users);
        return $users[$targetIndex];
    }

    public static function patch(int $id, array $data): ?array
    {
        $users = self::loadAll();
        $targetIndex = -1;

        foreach ($users as $index => $u) {
            if (isset($u['id']) && $u['id'] === $id) {
                $targetIndex = $index;
                break;
            }
        }

        if ($targetIndex === -1) {
            return null;
        }

        if (isset($data['name'])) {
            $users[$targetIndex]['name'] = trim($data['name']);
        }
        if (isset($data['email'])) {
            $users[$targetIndex]['email'] = trim($data['email']);
        }
        if (isset($data['graduationYear'])) {
            $users[$targetIndex]['graduationYear'] = (int)$data['graduationYear'];
        }
        if (isset($data['department'])) {
            $users[$targetIndex]['department'] = trim($data['department']);
        }
        if (isset($data['company'])) {
            $users[$targetIndex]['company'] = trim($data['company']);
        }

        self::saveAll($users);
        return $users[$targetIndex];
    }

    public static function delete(int $id): ?array
    {
        $users = self::loadAll();
        $targetIndex = -1;

        foreach ($users as $index => $u) {
            if (isset($u['id']) && $u['id'] === $id) {
                $targetIndex = $index;
                break;
            }
        }

        if ($targetIndex === -1) {
            return null;
        }

        $deletedUser = $users[$targetIndex];
        array_splice($users, $targetIndex, 1);
        self::saveAll($users);

        return $deletedUser;
    }
}
