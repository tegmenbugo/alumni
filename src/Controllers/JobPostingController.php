<?php
namespace App\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\User;
use App\Core\View;
use App\Core\Response;

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
     */
    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company_name'] ?? '');
        $userId = !empty($_POST['posted_by_user_id']) ? (int)$_POST['posted_by_user_id'] : 1;

        $message = null;
        if (!empty($title) && !empty($company)) {
            JobPosting::create([
                'posted_by_user_id' => $userId,
                'title'              => $title,
                'company_name'       => $company,
                'employment_type'    => $_POST['employment_type'] ?? 'Tam Zamanlı',
                'workplace_type'     => $_POST['workplace_type'] ?? 'Hibrit',
                'location'           => $_POST['location'] ?? 'İstanbul, Türkiye',
                'experience_level'   => $_POST['experience_level'] ?? 'Giriş Seviyesi',
                'salary_min'         => !empty($_POST['salary_min']) ? (float)$_POST['salary_min'] : null,
                'salary_max'         => !empty($_POST['salary_max']) ? (float)$_POST['salary_max'] : null,
                'salary_currency'    => $_POST['salary_currency'] ?? 'TRY',
                'description'        => trim($_POST['description'] ?? 'Detaylı ilan açıklaması.'),
                'requirements'       => trim($_POST['requirements'] ?? ''),
                'contact_email'      => trim($_POST['contact_email'] ?? ''),
                'application_url'    => trim($_POST['application_url'] ?? ''),
                'status'             => 'active',
                'deadline'           => !empty($_POST['deadline']) ? $_POST['deadline'] : null
            ]);
            $message = "🎉 '{$title}' ({$company}) ilanı başarıyla yayınlandı!";
        }

        $jobs = JobPosting::all();
        $users = User::all();
        $totalJobs = JobPosting::count();

        View::render('jobs/index', [
            'jobs'      => $jobs,
            'users'     => $users,
            'totalJobs' => $totalJobs,
            'filters'   => [],
            'message'   => $message
        ]);
    }

    /**
     * POST /jobs/{id}/apply - İlana Başvuru Yapma (Web Formu)
     */
    public function apply(string $id): void
    {
        $jobId = (int)$id;
        $job = JobPosting::find($jobId);

        if (!$job) {
            http_response_code(404);
            echo "İlan bulunamadı.";
            exit;
        }

        $applicantId = !empty($_POST['applicant_user_id']) ? (int)$_POST['applicant_user_id'] : 1;
        $coverLetter = trim($_POST['cover_letter'] ?? '');

        JobApplication::create([
            'job_posting_id'    => $jobId,
            'applicant_user_id' => $applicantId,
            'status'             => 'applied',
            'cover_letter'       => $coverLetter
        ]);

        $applications = JobApplication::forPosting($jobId);
        $users = User::all();

        View::render('jobs/show', [
            'job'          => $job,
            'applications' => $applications,
            'users'        => $users,
            'message'      => "✅ Başvurunuz başarıyla kaydedildi!"
        ]);
    }
}
