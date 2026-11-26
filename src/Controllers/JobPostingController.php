<?php
namespace App\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use App\Core\View;
use App\Core\Response;
use App\Middlewares\AuthMiddleware;

/**
 * JobPostingController (MVC - Web View Katmanı)
 * İş ve staj ilanlarının tarayıcı arayüzü (UI) ve başvuru süreçlerini yönetir.
 * Doç. Dr. Emre Akadal · YBSB3001 Web Programming - Hafta 06 (Ödev / UI)
 */
class JobPostingController
{
    /**
     * GET /jobs - İş ve Staj İlanları Portalı (UI Listeleme)
     */
    public function index(): void
    {
        $filters = [
            'status'          => $_GET['status'] ?? 'active',
            'employment_type' => $_GET['type'] ?? null,
            'workplace_type'  => $_GET['workplace'] ?? null,
            'q'               => $_GET['q'] ?? null,
        ];

        $jobs = JobPosting::all(array_filter($filters));
        $users = User::all();
        $totalJobs = JobPosting::count();

        View::render('jobs/index', [
            'jobs'      => $jobs,
            'users'     => $users,
            'totalJobs' => $totalJobs,
            'filters'   => $filters,
            'message'   => null
        ]);
    }

    /**
     * GET /jobs/{id} - Tekil İlan Detayı & Başvuru Ekranı (UI)
     */
    public function show(string $id): void
    {
        $jobId = (int)$id;
        $job = JobPosting::find($jobId);

        if (!$job) {
            http_response_code(404);
            echo "404 - İş ilanı bulunamadı (ID: {$id})";
            exit;
        }

        $applications = JobApplication::forPosting($jobId);
        $users = User::all();

        View::render('jobs/show', [
            'job'          => $job,
            'applications' => $applications,
            'users'        => $users,
            'message'      => null
        ]);
    }

    /**
     * POST /jobs - Yeni İlan Yayınlama (Web Formu)
     * ddd.md Bölüm 8.3: Yalnızca alumnus, faculty ve admin iş ilanı yayınlayabilir.
     * student rolündeki kullanıcılar 403 Forbidden alır.
     */
    public function store(): void
    {
        // Hafta 09 (Görev 4): AuthMiddleware - İlan yayınlama yetki kontrolü
        $authUser = AuthMiddleware::requireCanPostJob(false);

        $errors = JobPosting::validate($_POST);
        $message = null;

        if (empty($errors)) {
            // Oturum açmış kullanıcının ID'sini otomatik olarak ilan sahibi yap
            $userId = (int)$authUser['id'];
            $title = trim($_POST['title']);
            $company = trim($_POST['company_name'] ?? ($_POST['company'] ?? ''));

            JobPosting::create([
                'posted_by_user_id' => $userId,
                'title'              => $title,
                'company_name'       => $company,
                'employment_type'    => $_POST['employment_type'] ?? 'Tam Zamanlı',
                'workplace_type'     => $_POST['workplace_type'] ?? 'Hibrit',
                'location'           => $_POST['location'] ?? ($_POST['city'] ?? 'İstanbul, Türkiye'),
                'experience_level'   => $_POST['experience_level'] ?? 'Giriş Seviyesi',
                'salary_min'         => !empty($_POST['salary_min']) ? (float)$_POST['salary_min'] : null,
                'salary_max'         => !empty($_POST['salary_max']) ? (float)$_POST['salary_max'] : null,
                'salary_currency'    => $_POST['salary_currency'] ?? 'TRY',
                'description'        => !empty($_POST['description']) ? trim($_POST['description']) : null,
                'requirements'       => trim($_POST['requirements'] ?? ''),
                'contact_email'      => trim($_POST['contact_email'] ?? ''),
                'application_url'    => trim($_POST['application_url'] ?? ''),
                'status'             => 'active',
                'deadline'           => !empty($_POST['deadline']) ? $_POST['deadline'] : null
            ]);
            $message = "🎉 '{$title}' ({$company}) ilanı başarıyla yayınlandı!";
        } else {
            $message = "⚠️ İlan oluşturulamadı: " . implode(' | ', $errors);
        }

        $jobs = JobPosting::all();
        $users = User::all();
        $totalJobs = JobPosting::count();

        View::render('jobs/index', [
            'jobs'      => $jobs,
            'users'     => $users,
            'totalJobs' => $totalJobs,
            'filters'   => [],
            'message'   => $message,
            'errors'    => $errors
        ]);
    }

    /**
     * POST /jobs/{id}/apply - İlana Başvuru Yapma (Web Formu)
     * ddd.md Bölüm 8.3: Yalnızca student ve alumnus başvurabilir.
     * Kendi ilanına başvuru yasağı (Conflict of Interest) denetlenir.
     */
    public function apply(string $id): void
    {
        $jobId = (int)$id;
        $job = JobPosting::find($jobId);

        if (!$job) {
            http_response_code(404);
            echo "404 - İş ilanı bulunamadı (ID: {$id})";
            exit;
        }

        // Hafta 09 (Görev 4): AuthMiddleware - Başvuru yetkisi ve çıkar çatışması kontrolü
        $authUser = AuthMiddleware::requireCanApplyJob($job, false);

        $applicantId = (int)$authUser['id'];
        $coverLetter = trim($_POST['cover_letter'] ?? '');
        $resumeUrl   = trim($_POST['resume_url'] ?? '');
        $portfolioUrl = trim($_POST['portfolio_url'] ?? '');

        JobApplication::create([
            'job_posting_id'    => $jobId,
            'applicant_user_id' => $applicantId,
            'status'            => 'applied',
            'cover_letter'      => $coverLetter,
            'resume_url'        => $resumeUrl !== '' ? $resumeUrl : null,
            'portfolio_url'     => $portfolioUrl !== '' ? $portfolioUrl : null,
        ]);

        $applications = JobApplication::forPosting($jobId);
        $users = User::all();

        View::render('jobs/show', [
            'job'          => $job,
            'applications' => $applications,
            'users'        => $users,
            'message'      => "✅ Başvurunuz başarıyla kaydedildi! ({$authUser['name']})"
        ]);
    }
}
