<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\View;
use App\Core\Response;
use App\Middlewares\AuthMiddleware;

/**
 * UserController (MVC - Web Controller Katmanı)
 * Web tarayıcısı üzerinden mezun/kullanıcı HTML arayüz CRUD işlemlerini yönetir.
 * Tüm metodlar JSON değil; View katmanı (HTML) döndürür.
 *
 * CRUD Rotaları:
 *   READ   → GET  /users             → index()
 *   READ   → GET  /users/{id}        → show($id)
 *   READ   → GET  /users/{id}/edit   → edit($id)
 *   CREATE → POST /users             → store()
 *   UPDATE → POST /users/{id}/update → update($id)
 *   DELETE → POST /users/{id}/delete → destroy($id)
 */
class UserController
{
    // -------------------------------------------------------------------------
    // READ ALL — GET /users
    // -------------------------------------------------------------------------
    /**
     * Mezun listesi arayüzü ve inline kayıt formu (View Layer)
     */
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $users = !empty($search) ? User::search($search) : User::all();
        $totalCount = User::count();

        View::render('users/index', [
            'users'      => $users,
            'totalCount' => $totalCount,
            'search'     => $search
        ]);
    }

    // -------------------------------------------------------------------------
    // READ ONE — GET /users/{id}
    // -------------------------------------------------------------------------
    /**
     * Tekil mezun profil kartı (View Layer)
     */
    public function show(string $id): void
    {
        // Hafta 09 (Görev 4): Detay profil görüntülemek için oturum açmış olma şartı (ddd.md Bölüm 8.3)
        AuthMiddleware::requireAuth(false);

        $user = User::find((int)$id);

        if (!$user) {
            http_response_code(404);
            echo "404 — Mezun bulunamadı (ID: " . htmlspecialchars($id) . ")";
            exit;
        }

        View::render('users/show', ['user' => $user]);
    }

    // -------------------------------------------------------------------------
    // EDIT FORM — GET /users/{id}/edit
    // -------------------------------------------------------------------------
    /**
     * Mezun düzenleme formu (View Layer — yalnızca kendi profili veya admin)
     */
    public function edit(string $id): void
    {
        // Hafta 09 (Görev 4): Sahiplik kontrolü (auth.id === target.id veya admin)
        AuthMiddleware::requireCanEditUser((int)$id, false);

        $user = User::find((int)$id);

        if (!$user) {
            http_response_code(404);
            echo "404 — Mezun bulunamadı (ID: " . htmlspecialchars($id) . ")";
            exit;
        }

        View::render('users/edit', ['user' => $user, 'message' => null]);
    }

    // -------------------------------------------------------------------------
    // CREATE — POST /users
    // -------------------------------------------------------------------------
    /**
     * Formdan yeni mezun kaydı; kayıt sonrası güncel liste View'i döndürür (Sadece Admin)
     */
    public function store(): void
    {
        // Hafta 09 (Görev 4): Doğrudan kullanıcı ekleme yalnızca admin yetkisindedir (normal kullanıcılar /register kullanır)
        AuthMiddleware::requireRole(['admin'], false);

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $year    = !empty($_POST['graduationYear']) ? (int)$_POST['graduationYear'] : null;
        $dept    = trim($_POST['department'] ?? '');
        $company = trim($_POST['company'] ?? '');

        $message = null;

        if (!empty($name)) {
            User::create([
                'name'           => $name,
                'email'          => $email,
                'graduationYear' => $year,
                'department'     => $dept,
                'company'        => $company,
            ]);
            $message = "✅ '{$name}' isimli mezun başarıyla eklendi! (POST /users → UserController::store)";
        }

        // View Layer: redirect yerine güncel listeyi HTML olarak render et
        $users = User::all();
        View::render('users/index', [
            'users'   => $users,
            'message' => $message,
        ]);
    }

    // -------------------------------------------------------------------------
    // UPDATE — POST /users/{id}/update
    // -------------------------------------------------------------------------
    /**
     * Formdan gelen verilerle mezun güncelleme; sonrası edit view döndürür
     */
    public function update(string $id): void
    {
        $userId  = (int)$id;
        // Hafta 09 (Görev 4): Sahiplik kontrolü (auth.id === target.id veya admin)
        AuthMiddleware::requireCanEditUser($userId, false);

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $year    = !empty($_POST['graduationYear']) ? (int)$_POST['graduationYear'] : null;
        $dept    = trim($_POST['department'] ?? '');
        $company = trim($_POST['company'] ?? '');

        $message = null;

        if (!empty($name)) {
            User::update($userId, [
                'name'           => $name,
                'email'          => $email,
                'graduationYear' => $year,
                'department'     => $dept,
                'company'        => $company,
            ]);
            $message = "✅ Mezun bilgileri başarıyla güncellendi! (POST /users/{$userId}/update → UserController::update)";
        }

        // View Layer: güncellenmiş kayıtla edit formunu tekrar render et
        $user = User::find($userId);
        if (!$user) {
            Response::redirect('/users');
        }

        View::render('users/edit', [
            'user'    => $user,
            'message' => $message,
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE — POST /users/{id}/delete
    // -------------------------------------------------------------------------
    /**
     * Mezun sil; silme sonrası güncel liste View'ini döndürür
     */
    public function destroy(string $id): void
    {
        // Hafta 09 (Görev 4): Kullanıcı silme yalnızca admin yetkisindedir (ddd.md Bölüm 8.3)
        AuthMiddleware::requireRole(['admin'], false);

        $userId = (int)$id;
        $user   = User::find($userId);
        $name   = $user ? $user['name'] : "ID #{$userId}";

        User::delete($userId);

        // View Layer: redirect yerine güncel listeyi HTML olarak render et
        $users = User::all();
        View::render('users/index', [
            'users'   => $users,
            'message' => "🗑️ '{$name}' isimli mezun silindi. (POST /users/{$userId}/delete → UserController::destroy)",
        ]);
    }
}
