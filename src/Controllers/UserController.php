<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Response;

/**
 * UserController (MVC - Controller Katmanı)
 * /api/users CRUD işlemlerinin iş mantığını yönetir.
 */
class UserController
{
    private function getJsonInput(): ?array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return null;
        }
        return json_decode($raw, true);
    }

    /**
     * GET /api/users - Tüm mezunları listele (Read All)
     */
    public function index(): void
    {
        $users = User::all();
        Response::json($users, 200);
    }

    /**
     * GET /api/users/{id} - ID ile tek mezun getir (Read One)
     */
    public function show(string $id): void
    {
        $userId = (int)$id;
        $user = User::find($userId);

        if (!$user) {
            Response::json(['error' => "ID {$userId} numaralı kullanıcı bulunamadı."], 404);
        }

        Response::json($user, 200);
    }

    /**
     * POST /api/users - Yeni mezun oluştur (Create)
     */
    public function store(): void
    {
        $input = $this->getJsonInput();

        if (!$input || empty(trim($input['name'] ?? ''))) {
            Response::json([
                'error' => "Geçersiz istek. 'name' alanı zorunludur.",
                'example' => [
                    'name' => 'Elif Kaya',
                    'graduationYear' => 2024,
                    'email' => 'elif.kaya@alumni.iu.edu.tr',
                    'department' => 'Yönetim Bilişim Sistemleri',
                    'company' => 'Tech Corp'
                ]
            ], 400);
        }

        $newUser = User::create($input);
        Response::json($newUser, 201);
    }

    /**
     * PUT /api/users/{id} - Mezun bilgilerini tamamen güncelle (Full Update)
     */
    public function update(string $id): void
    {
        $userId = (int)$id;
        $input = $this->getJsonInput();

        if (!$input || empty(trim($input['name'] ?? ''))) {
            Response::json(['error' => "Geçersiz istek. 'name' alanı zorunludur."], 400);
        }

        $updated = User::update($userId, $input);

        if (!$updated) {
            Response::json(['error' => "ID {$userId} numaralı kullanıcı bulunamadı."], 404);
        }

        Response::json($updated, 200);
    }

    /**
     * PATCH /api/users/{id} - Mezun alanını kısmen güncelle (Partial Update)
     */
    public function patch(string $id): void
    {
        $userId = (int)$id;
        $input = $this->getJsonInput();

        if (!$input) {
            Response::json(['error' => 'Güncellenecek geçerli bir JSON gövdesi gönderiniz.'], 400);
        }

        $updated = User::patch($userId, $input);

        if (!$updated) {
            Response::json(['error' => "ID {$userId} numaralı kullanıcı bulunamadı."], 404);
        }

        Response::json($updated, 200);
    }

    /**
     * DELETE /api/users/{id} - Mezun kaydını sil (Delete)
     */
    public function destroy(string $id): void
    {
        $userId = (int)$id;
        $deleted = User::delete($userId);

        if (!$deleted) {
            Response::json(['error' => "ID {$userId} numaralı kullanıcı bulunamadı."], 404);
        }

        Response::json([
            'message' => "ID {$userId} numaralı kullanıcı başarıyla silindi.",
            'deletedUser' => $deleted
        ], 200);
    }
}
