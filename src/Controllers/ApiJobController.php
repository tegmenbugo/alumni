<?php
namespace App\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use App\Core\Response;

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
     */
    public function store(): void
    {
        $data = $this->getJsonInput();

        if (empty($data['title']) || empty($data['company_name'])) {
            Response::json(['error' => 'title ve company_name alanları zorunludur.'], 400);
            return;
        }

        if (empty($data['posted_by_user_id'])) {
            // Varsayılan ilk mezun kullanıcıyı ata
            $users = User::all();
            $data['posted_by_user_id'] = !empty($users) ? $users[0]['id'] : 1;
        }

        $job = JobPosting::create($data);
        Response::json($job, 201);
    }

    /**
     * GET /api/jobs/{id} - Tekil ilan detayını ve başvuru sayısını getir
     */
    public function show(string $id): void
    {
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
        $jobId = (int)$id;
        $data = $this->getJsonInput();

        $updated = JobPosting::update($jobId, $data);
        if (!$updated) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

        Response::json($updated, 200);
    }

    /**
     * DELETE /api/jobs/{id} - İlanı sil
     */
    public function destroy(string $id): void
    {
        $deleted = JobPosting::delete((int)$id);
        if (!$deleted) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $id . ')'], 404);
            return;
        }

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
        $uId = (int)$userId;
        $user = User::find($uId);

        if (!$user) {
            Response::json(['error' => 'Kullanıcı bulunamadı (ID: ' . $userId . ')'], 404);
            return;
        }

        $data = $this->getJsonInput();
        $data['posted_by_user_id'] = $uId;

        if (empty($data['title']) || empty($data['company_name'])) {
            Response::json(['error' => 'title ve company_name alanları zorunludur.'], 400);
            return;
        }

        $job = JobPosting::create($data);
        Response::json($job, 201);
    }

    /**
     * NESTED: GET /api/jobs/{jobId}/applications
     * Belirli bir ilana yapılan tüm başvuruları listeler.
     */
    public function jobApplications(string $jobId): void
    {
        $jId = (int)$jobId;
        $job = JobPosting::find($jId);

        if (!$job) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $jobId . ')'], 404);
            return;
        }

        $applications = JobApplication::forPosting($jId);
        Response::json([
            'job'          => ['id' => $job['id'], 'title' => $job['title'], 'company' => $job['company_name']],
            'count'        => count($applications),
            'applications' => $applications
        ], 200);
    }

    /**
     * NESTED: POST /api/jobs/{jobId}/applications
     * Belirli bir ilana yeni başvuru yapar.
     */
    public function applyJob(string $jobId): void
    {
        $jId = (int)$jobId;
        $job = JobPosting::find($jId);

        if (!$job) {
            Response::json(['error' => 'İş ilanı bulunamadı (ID: ' . $jobId . ')'], 404);
            return;
        }

        $data = $this->getJsonInput();
        $data['job_posting_id'] = $jId;

        if (empty($data['applicant_user_id'])) {
            Response::json(['error' => 'applicant_user_id alanı zorunludur.'], 400);
            return;
        }

        $app = JobApplication::create($data);
        Response::json([
            'message'     => 'Başvurunuz başarıyla kaydedildi.',
            'application' => $app
        ], 201);
    }

    /**
     * NESTED: GET /api/users/{userId}/applications
     * Belirli bir mezun/öğrencinin yaptığı tüm başvuruları listeler.
     */
    public function userApplications(string $userId): void
    {
        $uId = (int)$userId;
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
