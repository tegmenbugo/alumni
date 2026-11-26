<?php
namespace App\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use App\Core\Response;
use App\Middlewares\AuthMiddleware;

/**
 * ApiJobController (REST API Controller)
 * İş ve staj ilanları için doğrudan (direct) ve ilişkisel (nested) API rotalarını yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 06 (Görev 4 & 5)
 */
class ApiJobController
{
    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    // =========================================================================
    // DOĞRUDAN ROTALAR (DIRECT ROUTES)
    // =========================================================================

    /**
     * GET /api/jobs - Tüm ilanları listele (Filtreleme & Arama destekli)
     */
    public function index(): void
    {
        $filters = [
            'status'          => $_GET['status'] ?? null,
            'employment_type' => $_GET['type'] ?? null,
            'workplace_type'  => $_GET['workplace'] ?? null,
            'company'         => $_GET['company'] ?? null,
            'q'               => $_GET['q'] ?? null,
        ];

        $jobs = JobPosting::all(array_filter($filters));
        Response::json($jobs, 200);
    }

    /**
     * POST /api/jobs - Yeni iş ilanı oluştur
     * ddd.md Hafta 07 İş Kuralları + Hafta 09 AuthMiddleware (Sadece alumnus, faculty, admin)
     */
    public function store(): void
    {
        // Hafta 09 (Görev 4): AuthMiddleware - Yalnızca alumnus, faculty, admin ilan yayınlayabilir
        $authUser = AuthMiddleware::requireCanPostJob(true);

        $raw = file_get_contents('php://input');
        if (trim($raw) === '') {
            Response::json([
                'error' => 'Boş istek gövdesi (Empty request body)',
                'message' => 'Lütfen JSON formatında geçerli bir veri gönderin.'
            ], 400);
            return;
        }

        $data = json_decode($raw, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'error' => 'Geçersiz JSON sözdizimi (Invalid JSON syntax)',
                'message' => 'JSON ayrıştırılamadı: ' . json_last_error_msg()
            ], 400);
            return;
        }

        // Hafta 07: ddd.md Doğrulama Kuralları
        $errors = JobPosting::validate($data);

        // Admin değilse ilan sahibi daima oturum açmış kullanıcıdır; admin başkası adına açabilir
        if ($authUser['role'] === 'admin' && !empty($data['posted_by_user_id'])) {
            $targetUser = User::find((int)$data['posted_by_user_id']);
            if (!$targetUser) {
                $errors['posted_by_user_id'] = 'Belirtilen kullanıcı (posted_by_user_id: ' . (int)$data['posted_by_user_id'] . ') sistemde mevcut değil.';
            }
        } else {
            $data['posted_by_user_id'] = (int)$authUser['id'];
        }

        if (!empty($errors)) {
            Response::json([
                'error'   => 'Doğrulama hatası (Validation Failed)',
                'message' => 'Gönderilen ilan verileri ddd.md iş kurallarına uymuyor.',
                'errors'  => $errors
            ], 422);
            return;
        }

        $job = JobPosting::create($data);
        Response::json([
            'message' => 'İş ilanı başarıyla oluşturuldu.',
            'data'    => $job
        ], 201);
    }

    /**
     * GET /api/jobs/{id} - Tekil ilan detayını ve başvuru sayısını getir
     */
    public function show(string $id): void
    {
        if (!is_numeric($id) || (int)$id <= 0) {
            Response::json([
                'error'   => 'Geçersiz parametre (Invalid ID)',
                'message' => 'İlan ID değeri pozitif bir tamsayı olmalıdır.'
            ], 400);
            return;
        }

        $job = JobPosting::find((int)$id);

        if (!$job) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        $job['applications_count'] = count(JobPosting::applications((int)$id));
        Response::json($job, 200);
    }

    /**
     * PUT /api/jobs/{id} - İlan bilgilerini güncelle
     */
    public function update(string $id): void
    {
        if (!is_numeric($id) || (int)$id <= 0) {
            Response::json([
                'error'   => 'Geçersiz parametre (Invalid ID)',
                'message' => 'İlan ID değeri pozitif bir tamsayı olmalıdır.'
            ], 400);
            return;
        }

        $jobId = (int)$id;
        $existing = JobPosting::find($jobId);
        if (!$existing) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        // Hafta 09 (Görev 4): AuthMiddleware - Yalnızca ilanın sahibi veya admin güncelleyebilir
        AuthMiddleware::requireCanManageJob($existing, true);

        $raw = file_get_contents('php://input');
        if (trim($raw) === '') {
            Response::json([
                'error' => 'Boş istek gövdesi',
                'message' => 'Güncelleme için JSON formatında alanlar gönderilmelidir.'
            ], 400);
            return;
        }

        $data = json_decode($raw, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'error' => 'Geçersiz JSON sözdizimi',
                'message' => json_last_error_msg()
            ], 400);
            return;
        }

        // Güncelleme validasyonu
        $errors = JobPosting::validate($data, true);
        if (!empty($errors)) {
            Response::json([
                'error'   => 'Doğrulama hatası (Validation Failed)',
                'message' => 'Güncellenmek istenen veriler kurallara uymuyor.',
                'errors'  => $errors
            ], 422);
            return;
        }

        $updated = JobPosting::update($jobId, $data);
        Response::json([
            'message' => 'İş ilanı başarıyla güncellendi.',
            'data'    => $updated
        ], 200);
    }

    /**
     * DELETE /api/jobs/{id} - İlanı sil
     */
    public function destroy(string $id): void
    {
        $jobId = (int)$id;
        $existing = JobPosting::find($jobId);
        if (!$existing) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        // Hafta 09 (Görev 4): AuthMiddleware - Yalnızca ilanın sahibi veya admin silebilir
        AuthMiddleware::requireCanManageJob($existing, true);

        $deleted = JobPosting::delete($jobId);
        Response::json(['message' => 'İş ilanı başarıyla silindi', 'job' => $deleted], 200);
    }

    // =========================================================================
    // İLİŞKİSEL / İÇ İÇE ROTALAR (NESTED ROUTES) - Görev 4 & 5
    // =========================================================================

    /**
     * NESTED: GET /api/users/{userId}/jobs
     * Belirli bir mezunun açtığı tüm iş ilanlarını listeler.
     */
    public function userJobs(string $userId): void
    {
        $uId = (int)$userId;
        $user = User::find($uId);

        if (!$user) {
            Response::json(['error' => 'Kullanıcı bulunamadı (ID: ' . $userId . ')'], 404);
            return;
        }

        $jobs = JobPosting::byUser($uId);
        Response::json([
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'company' => $user['company']],
            'count' => count($jobs),
            'jobs'  => $jobs
        ], 200);
    }

    /**
     * NESTED: POST /api/users/{userId}/jobs
     * Belirli bir mezun adına yeni bir iş ilanı oluşturur.
     */
    public function userStoreJob(string $userId): void
    {
        $authUser = AuthMiddleware::requireCanPostJob(true);

        $uId = (int)$userId;
        if ($authUser['role'] !== 'admin' && (int)$authUser['id'] !== $uId) {
            AuthMiddleware::denyForbidden(
                '🚫 Başka bir kullanıcı adına iş ilanı açamazsınız. Yalnızca kendi adınıza veya admin yetkisiyle ilan açabilirsiniz.',
                ['owner', 'admin'],
                true
            );
        }

        $user = User::find($uId);

        if (!$user) {
            Response::json(['error' => 'Kullanıcı bulunamadı (ID: ' . $userId . ')'], 404);
            return;
        }

        $data = $this->getJsonInput();
        $data['posted_by_user_id'] = $uId;

        $errors = JobPosting::validate($data);
        if (!empty($errors)) {
            Response::json([
                'error'   => 'Doğrulama hatası (Validation Failed)',
                'message' => 'Gönderilen ilan verileri kurallara uymuyor.',
                'errors'  => $errors
            ], 422);
            return;
        }

        $job = JobPosting::create($data);
        Response::json($job, 201);
    }

    /**
     * NESTED: GET /api/jobs/{jobId}/applications
     * Belirli bir ilana yapılan tüm başvuruları listeler (Sadece ilan sahibi veya admin).
     */
    public function jobApplications(string $jobId): void
    {
        $jId = (int)$jobId;
        $job = JobPosting::find($jId);

        if (!$job) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $jobId . ')'], 404);
            return;
        }

        AuthMiddleware::requireCanManageJob($job, true);

        $applications = JobApplication::forPosting($jId);
        Response::json([
            'job'          => ['id' => $job['id'], 'title' => $job['title'], 'company' => $job['company_name']],
            'count'        => count($applications),
            'applications' => $applications
        ], 200);
    }

    /**
     * NESTED: POST /api/jobs/{jobId}/applications
     * Belirli bir ilana yeni başvuru yapar (Sadece student/alumnus; kendi ilanına başvuramaz).
     */
    public function applyJob(string $jobId): void
    {
        $jId = (int)$jobId;
        $job = JobPosting::find($jId);

        if (!$job) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $jobId . ')'], 404);
            return;
        }

        $authUser = AuthMiddleware::requireCanApplyJob($job, true);

        $data = $this->getJsonInput();
        $data['job_posting_id'] = $jId;
        $data['applicant_user_id'] = (int)$authUser['id'];

        $app = JobApplication::create($data);
        Response::json([
            'message'     => 'Başvurunuz başarıyla kaydedildi.',
            'application' => $app
        ], 201);
    }

    /**
     * NESTED: GET /api/users/{userId}/applications
     * Belirli bir mezun/öğrencinin yaptığı tüm başvuruları listeler (Sadece kendisi veya admin).
     */
    public function userApplications(string $userId): void
    {
        $uId = (int)$userId;
        AuthMiddleware::requireCanEditUser($uId, true);

        $user = User::find($uId);

        if (!$user) {
            Response::json(['error' => 'Kullanıcı bulunamadı (ID: ' . $userId . ')'], 404);
            return;
        }

        $apps = JobApplication::forUser($uId);
        Response::json([
            'applicant'    => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']],
            'count'        => count($apps),
            'applications' => $apps
        ], 200);
    }
}
