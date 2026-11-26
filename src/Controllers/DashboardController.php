<?php
namespace App\Controllers;

use App\Core\View;
use App\Middlewares\AuthMiddleware;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\JobApplication;

/**
 * DashboardController (Kullanıcıya Özel Sayfalar - Hafta 09 Görev 7)
 * Oturum açmış kullanıcının kendi rolüne özel kişisel panelini (/dashboard),
 * kendi ilanlarını (/my-jobs) ve kendi başvurularını (/my-applications) yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming
 */
class DashboardController
{
    /**
     * GET /dashboard - Giriş yapmış kullanıcının rolüne özel kişisel çalışma alanı
     */
    public function index(): void
    {
        $authUser = AuthMiddleware::requireAuth(false);
        $userId = (int)$authUser['id'];
        $role = $authUser['role'] ?? 'alumnus';

        $fullUser       = User::find($userId) ?? $authUser;
        $profile        = User::profile($userId);
        $canPostJob     = AuthMiddleware::canPostJob($authUser);
        $canApplyJob    = in_array($role, ['student', 'alumnus'], true);

        $myJobs         = $canPostJob ? JobPosting::byUser($userId) : [];
        $myApplications = $canApplyJob ? JobApplication::forUser($userId) : [];
        $openJobs       = array_slice(JobPosting::all(['status' => 'active']), 0, 4);

        // İlan sahibi için toplam gelen başvuru sayısını hesapla
        $incomingCount = 0;
        foreach ($myJobs as &$mj) {
            $apps = JobApplication::forPosting((int)$mj['id']);
            $mj['applicant_count'] = count($apps);
            $incomingCount += count($apps);
        }
        unset($mj);

        View::render('dashboard/index', [
            'user'           => $fullUser,
            'profile'        => $profile,
            'canPostJob'     => $canPostJob,
            'canApplyJob'    => $canApplyJob,
            'myJobs'         => $myJobs,
            'myApplications' => $myApplications,
            'incomingCount'  => $incomingCount,
            'openJobs'       => $openJobs,
        ]);
    }

    /**
     * GET /my-applications - Kullanıcının kendi iş/staj başvurularını görüntülediği sayfa
     * Yalnızca 'student' ve 'alumnus' rollerine açıktır.
     */
    public function myApplications(): void
    {
        $authUser = AuthMiddleware::requireRole(['student', 'alumnus'], false);
        $userId = (int)$authUser['id'];

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $flashSuccess = $_SESSION['user_flash_success'] ?? null;
        $flashError   = $_SESSION['user_flash_error'] ?? null;
        unset($_SESSION['user_flash_success'], $_SESSION['user_flash_error']);

        $applications = JobApplication::forUser($userId);

        View::render('dashboard/applications', [
            'user'         => $authUser,
            'applications' => $applications,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ]);
    }

    /**
     * POST /my-applications/{id}/withdraw - Kullanıcının kendi başvurusunu geri çekmesi
     */
    public function withdrawApplication(string $id): void
    {
        $authUser = AuthMiddleware::requireRole(['student', 'alumnus'], false);
        $userId = (int)$authUser['id'];
        $appId  = (int)$id;

        $app = JobApplication::find($appId);
        if (!$app) {
            http_response_code(404);
            echo "404 - Başvuru bulunamadı (ID: {$appId})";
            exit;
        }

        // Sahiplik Kontrolü: Başkasına ait başvuruyu geri çekemez!
        if ((int)$app['applicant_user_id'] !== $userId) {
            AuthMiddleware::denyForbidden(
                '🚫 Sahiplik Kuralı: Yalnızca kendi yaptığınız başvuruları geri çekebilirsiniz.',
                ['owner (applicant)'],
                false,
                'Yetkisiz Başvuru İşlemi (403 Forbidden)'
            );
        }

        JobApplication::updateStatus($appId, 'withdrawn');

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user_flash_success'] = "✅ #{$appId} numaralı başvurunuz geri çekildi (withdrawn).";
        header('Location: /my-applications');
        exit;
    }

    /**
     * GET /my-jobs - İlan yayınlayabilen rollerin (alumnus, faculty, admin) kendi ilanları
     * ve bu ilanlara gelen başvuruları yönettiği sayfa.
     */
    public function myJobs(): void
    {
        $authUser = AuthMiddleware::requireCanPostJob(false);
        $userId = (int)$authUser['id'];

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $flashSuccess = $_SESSION['user_flash_success'] ?? null;
        $flashError   = $_SESSION['user_flash_error'] ?? null;
        unset($_SESSION['user_flash_success'], $_SESSION['user_flash_error']);

        $myJobs = JobPosting::byUser($userId);
        foreach ($myJobs as &$job) {
            $job['applications'] = JobApplication::forPosting((int)$job['id']);
        }
        unset($job);

        View::render('dashboard/my_jobs', [
            'user'         => $authUser,
            'myJobs'       => $myJobs,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ]);
    }

    /**
     * POST /my-jobs/{jobId}/status - İlan sahibinin kendi ilan durumunu güncellemesi
     */
    public function updateMyJobStatus(string $jobId): void
    {
        $jId = (int)$jobId;
        $job = JobPosting::find($jId);
        if (!$job) {
            http_response_code(404);
            echo "404 - İş ilanı bulunamadı (ID: {$jId})";
            exit;
        }

        AuthMiddleware::requireCanManageJob($job, false);

        $status = trim($_POST['status'] ?? 'active');
        $allowed = ['active', 'closed', 'draft', 'archived'];

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!in_array($status, $allowed, true)) {
            $_SESSION['user_flash_error'] = '⚠️ Geçersiz ilan durumu seçildi.';
        } else {
            JobPosting::update($jId, ['status' => $status]);
            $_SESSION['user_flash_success'] = "✅ '{$job['title']}' ilanı '{$status}' durumuna güncellendi.";
        }

        header('Location: /my-jobs');
        exit;
    }

    /**
     * POST /my-jobs/{jobId}/applications/{appId}/status - İlan sahibinin gelen başvuruyu değerlendirmesi
     */
    public function updateApplicantStatus(string $jobId, string $appId): void
    {
        $jId = (int)$jobId;
        $aId = (int)$appId;

        $job = JobPosting::find($jId);
        if (!$job) {
            http_response_code(404);
            echo "404 - İş ilanı bulunamadı (ID: {$jId})";
            exit;
        }

        AuthMiddleware::requireCanManageJob($job, false);

        $app = JobApplication::find($aId);
        if (!$app || (int)$app['job_posting_id'] !== $jId) {
            http_response_code(404);
            echo "404 - Bu ilana ait başvuru bulunamadı.";
            exit;
        }

        $status  = trim($_POST['status'] ?? 'reviewing');
        $allowed = ['applied', 'reviewing', 'interview', 'accepted', 'rejected'];

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!in_array($status, $allowed, true)) {
            $_SESSION['user_flash_error'] = '⚠️ Geçersiz değerlendirme durumu.';
        } else {
            JobApplication::updateStatus($aId, $status);
            $_SESSION['user_flash_success'] = "✅ Aday '{$app['applicant_name']}' başvurusu '{$status}' aşamasına alındı.";
        }

        header('Location: /my-jobs');
        exit;
    }
}
