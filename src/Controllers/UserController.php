<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\View;
use App\Core\Response;

/**
 * UserController (MVC - Web Controller Katmanı)
 * Web tarayıcısı üzerinden mezun/kullanıcı HTML arayüz CRUD işlemlerini yönetir.
 */
class UserController
{
    /**
     * GET /users - Mezun listesi arayüzü (READ ALL)
     */
    public function index(): void
    {
        $users = User::all();
        View::render('users/index', ['users' => $users]);
    }

    /**
     * GET /users/{id} - Tekil mezun profil kartı (READ ONE)
     */
    public function show(string $id): void
    {
        $userId = (int)$id;
        $user = User::find($userId);

        if (!$user) {
            http_response_code(404);
            echo "Kullanıcı bulunamadı (ID: {$userId})";
            exit;
        }

        View::render('users/show', ['user' => $user]);
    }

    /**
     * POST /users - Form üzerinden yeni mezun kaydı (CREATE)
     */
    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');

        if (!empty($name)) {
            User::create([
                'name'           => $name,
                'email'          => trim($_POST['email'] ?? ''),
                'graduationYear' => !empty($_POST['graduationYear']) ? (int)$_POST['graduationYear'] : null,
                'department'     => trim($_POST['department'] ?? ''),
                'company'        => trim($_POST['company'] ?? '')
            ]);
        }

        Response::redirect('/users');
    }

    /**
     * POST /users/{id}/update - Mezun bilgilerini formdan güncelleme (UPDATE)
     */
    public function update(string $id): void
    {
        $userId = (int)$id;
        $name = trim($_POST['name'] ?? '');

        if (!empty($name)) {
            User::update($userId, [
                'name'           => $name,
                'email'          => trim($_POST['email'] ?? ''),
                'graduationYear' => !empty($_POST['graduationYear']) ? (int)$_POST['graduationYear'] : null,
                'department'     => trim($_POST['department'] ?? ''),
                'company'        => trim($_POST['company'] ?? '')
            ]);
        }

        Response::redirect('/users');
    }

    /**
     * POST /users/{id}/delete veya DELETE /users/{id} - Mezun kaydını silme (DELETE)
     */
    public function destroy(string $id): void
    {
        $userId = (int)$id;
        User::delete($userId);
        Response::redirect('/users');
    }
}
