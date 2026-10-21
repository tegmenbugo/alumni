<?php
namespace App\Controllers;

use App\Models\JobApplication;
use App\Core\Response;

/**
 * ApiApplicationController (REST API Controller)
 * Başvurular için doğrudan (direct) yönetim uç noktaları.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 06 (Görev 4 & 5)
 */
class ApiApplicationController
{
    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * GET /api/applications - Tüm başvuruları listele
     */
    public function index(): void
    {
        $filters = [
            'status' => $_GET['status'] ?? null,
            'job_id' => $_GET['job_id'] ?? null
        ];

        $apps = JobApplication::all(array_filter($filters));
        Response::json($apps, 200);
    }

    /**
     * GET /api/applications/{id} - Tekil başvuru detayı
     */
    public function show(string $id): void
    {
        $app = JobApplication::find((int)$id);

        if (!$app) {
            Response::json(['error' => 'Başvuru bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        Response::json($app, 200);
    }

    /**
     * PATCH /api/applications/{id} - Başvuru durumunu güncelle (applied, reviewing, interview, accepted, rejected)
     */
    public function updateStatus(string $id): void
    {
        $data = $this->getJsonInput();
        $status = $data['status'] ?? null;

        if (!$status) {
            Response::json(['error' => 'status alanı zorunludur (applied, reviewing, interview, accepted, rejected)'], 400);
            return;
        }

        $updated = JobApplication::updateStatus((int)$id, $status);
        if (!$updated) {
            Response::json(['error' => 'Başvuru bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        Response::json([
            'message'     => 'Başvuru durumu güncellendi',
            'application' => $updated
        ], 200);
    }

    /**
     * DELETE /api/applications/{id} - Başvuruyu geri çek / sil
     */
    public function destroy(string $id): void
    {
        $deleted = JobApplication::delete((int)$id);

        if (!$deleted) {
            Response::json(['error' => 'Başvuru bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        Response::json([
            'message'     => 'Başvuru başarıyla silindi',
            'application' => $deleted
        ], 200);
    }
}
