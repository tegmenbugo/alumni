<?php
namespace App\Controllers;

use App\Core\Response;
use App\Core\View;

/**
 * ApiController (MVC - Controller Katmanı)
 * Sağlık kontrolü ve Swagger / OpenAPI dokümantasyonunu yönetir.
 */
class ApiController
{
    /**
     * GET /api/health - Servis sağlık kontrolü
     */
    public function health(): void
    {
        Response::json([
            'status'    => 'healthy',
            'service'   => 'Alumni Tracking System API',
            'timestamp' => date('c'),
            'version'   => '1.0.0'
        ], 200);
    }

    /**
     * GET /api/swagger - Swagger UI Görünümü
     */
    public function swagger(): void
    {
        View::render('swagger');
    }

    /**
     * GET /swagger - /api/swagger'a yönlendirme (Fallback)
     */
    public function swaggerRedirect(): void
    {
        Response::redirect('/api/swagger', 301);
    }

    /**
     * GET /api/migrations - ddd.md Veritabanı Migrasyon Durumu (Görev 5)
     */
    public function migrations(): void
    {
        try {
            $pdo = \App\Core\Database::connect();
            $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $statusList = \App\Core\Migration::getStatus($pdo);

            if ($driver === 'sqlite') {
                $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(\PDO::FETCH_COLUMN);
            } else {
                $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
            }

            Response::json([
                'status'     => 'success',
                'driver'     => $driver,
                'tables'     => $tables,
                'migrations' => $statusList,
                'schemaDoc'  => 'ddd.md'
            ], 200);
        } catch (\Exception $e) {
            Response::json(['error' => 'Migrasyon bilgisi alınamadı: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/openapi.json - OpenAPI 3.0 Şeması
     */
    public function openapi(): void
    {
        Response::json([
            'openapi' => '3.0.0',
            'info' => [
                'title'       => 'Alumni Tracking System API',
                'description' => "YBSB3001 Web Programming - MVC Architecture & REST Endpoints.\n\n• REST API uç noktaları ApiUserController tarafından yönetilmektedir.\n• Web HTML arayüzü rotaları (/users) UserController tarafından yönetilmektedir.\n• Kapsamlı Veri Tasarımı, ERD ve Veri Sözlüğü için ddd.md dokümanına bakınız.\n• Swagger/OpenAPI şeması her rota ve controller değişikliğinde sürekli güncel tutulur.",
                'version'     => '1.0.0'
            ],
            'tags' => [
                [
                    'name'        => 'ApiUserController',
                    'description' => 'Mezun REST API CRUD operasyonları (JSON girdi / JSON çıktı)'
                ],
                [
                    'name'        => 'JobPostings',
                    'description' => 'İş ve Staj İlanları Rotaları (Doğrudan ve İlişkisel Rotalar - Hafta 06)'
                ],
                [
                    'name'        => 'JobApplications',
                    'description' => 'İş Başvuruları Rotaları (Doğrudan ve Nested Rotalar - Hafta 06)'
                ],
                [
                    'name'        => 'System',
                    'description' => 'Sistem sağlığı ve meta bilgiler'
                ]
            ],
            'servers' => [
                ['url' => '/', 'description' => 'Mevcut Sunucu']
            ],
            'paths' => [
                '/api/health' => [
                    'get' => [
                        'tags'      => ['System'],
                        'summary'   => 'Sistem sağlık kontrolü (ApiController::health)',
                        'responses' => [
                            '200' => ['description' => 'Sistem çalışıyor']
                        ]
                    ]
                ],
                '/api/migrations' => [
                    'get' => [
                        'tags'      => ['System'],
                        'summary'   => 'ddd.md Veritabanı Migrasyon ve Tablo Durumu (ApiController::migrations)',
                        'responses' => [
                            '200' => ['description' => 'Migrasyon durumu ve ddd.md tabloları listesi']
                        ]
                    ]
                ],
                '/api/users' => [
                    'get' => [
                        'tags'      => ['ApiUserController'],
                        'summary'   => 'Tüm mezunları listele (Read All) - ApiUserController::index',
                        'responses' => [
                            '200' => ['description' => 'Mezun kullanıcı listesi (JSON)']
                        ]
                    ],
                    'post' => [
                        'tags'        => ['ApiUserController'],
                        'summary'     => 'Yeni mezun oluştur (Create) - ApiUserController::store',
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['name'],
                                        'properties' => [
                                            'name'           => ['type' => 'string', 'example' => 'Elif Kaya'],
                                            'email'          => ['type' => 'string', 'example' => 'elif.kaya@alumni.iu.edu.tr'],
                                            'graduationYear' => ['type' => 'integer', 'example' => 2024],
                                            'department'     => ['type' => 'string', 'example' => 'Yönetim Bilişim Sistemleri'],
                                            'company'        => ['type' => 'string', 'example' => 'Tech Solutions']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '201' => ['description' => 'Mezun başarıyla oluşturuldu'],
                            '400' => ['description' => 'Geçersiz istek (name zorunludur)']
                        ]
                    ]
                ],
                '/api/users/{id}' => [
                    'get' => [
                        'tags'       => ['ApiUserController'],
                        'summary'    => 'ID ile tek mezun getir (Read One) - ApiUserController::show',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Mezun bulundu'],
                            '404' => ['description' => 'Mezun bulunamadı']
                        ]
                    ],
                    'put' => [
                        'tags'       => ['ApiUserController'],
                        'summary'    => 'Mezunu tamamen güncelle (Full Update) - ApiUserController::update',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['name'],
                                        'properties' => [
                                            'name'           => ['type' => 'string', 'example' => 'Elif Kaya Demir'],
                                            'email'          => ['type' => 'string', 'example' => 'elif.demir@alumni.iu.edu.tr'],
                                            'graduationYear' => ['type' => 'integer', 'example' => 2024],
                                            'department'     => ['type' => 'string', 'example' => 'Yönetim Bilişim Sistemleri'],
                                            'company'        => ['type' => 'string', 'example' => 'Senior Tech']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Mezun güncellendi'],
                            '404' => ['description' => 'Mezun bulunamadı']
                        ]
                    ],
                    'patch' => [
                        'tags'       => ['ApiUserController'],
                        'summary'    => 'Mezun alanını kısmen güncelle (Partial Update) - ApiUserController::patch',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'properties' => [
                                            'company'        => ['type' => 'string', 'example' => 'Yeni Şirket A.Ş.'],
                                            'email'          => ['type' => 'string', 'example' => 'yeni.eposta@alumni.iu.edu.tr'],
                                            'graduationYear' => ['type' => 'integer', 'example' => 2025]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Mezun kısmen güncellendi'],
                            '404' => ['description' => 'Mezun bulunamadı']
                        ]
                    ],
                    'delete' => [
                        'tags'       => ['ApiUserController'],
                        'summary'    => 'Mezunu sil (Delete) - ApiUserController::destroy',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Mezun başarıyla silindi'],
                            '404' => ['description' => 'Mezun bulunamadı']
                        ]
                    ]
                ],
                '/api/jobs' => [
                    'get' => [
                        'tags'        => ['JobPostings'],
                        'summary'     => 'Tüm iş ve staj ilanlarını listele (Doğrudan Rota) - ApiJobController::index',
                        'parameters'  => [
                            ['name' => 'q', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']],
                            ['name' => 'type', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']]
                        ],
                        'responses'   => ['200' => ['description' => 'İlanlar listesi']]
                    ],
                    'post' => [
                        'tags'        => ['JobPostings'],
                        'summary'     => 'Yeni iş ilanı oluştur - ApiJobController::store',
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['title', 'company_name'],
                                        'properties' => [
                                            'title'             => ['type' => 'string', 'example' => 'Junior Backend Developer'],
                                            'company_name'      => ['type' => 'string', 'example' => 'Trendyol'],
                                            'posted_by_user_id' => ['type' => 'integer', 'example' => 1],
                                            'employment_type'   => ['type' => 'string', 'example' => 'Tam Zamanlı'],
                                            'workplace_type'    => ['type' => 'string', 'example' => 'Hibrit'],
                                            'description'       => ['type' => 'string', 'example' => 'Backend geliştirici pozisyonu']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses'   => ['201' => ['description' => 'İlan oluşturuldu']]
                    ]
                ],
                '/api/jobs/{id}' => [
                    'get' => [
                        'tags'       => ['JobPostings'],
                        'summary'    => 'Tekil iş ilanı detayı - ApiJobController::show',
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'responses'  => ['200' => ['description' => 'İlan detayı'], '404' => ['description' => 'İlan bulunamadı']]
                    ]
                ],
                '/api/jobs/{id}/applications' => [
                    'get' => [
                        'tags'       => ['JobApplications'],
                        'summary'    => 'İlana yapılan başvuruları listele (Nested Rota) - ApiJobController::jobApplications',
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'responses'  => ['200' => ['description' => 'İlana ait başvurular']]
                    ],
                    'post' => [
                        'tags'        => ['JobApplications'],
                        'summary'     => 'İlana başvuru yap (Nested Rota) - ApiJobController::applyJob',
                        'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema' => [
                                        'type'       => 'object',
                                        'required'   => ['applicant_user_id'],
                                        'properties' => [
                                            'applicant_user_id' => ['type' => 'integer', 'example' => 5],
                                            'cover_letter'      => ['type' => 'string', 'example' => 'İlana başvurmak istiyorum.']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses'   => ['201' => ['description' => 'Başvuru kaydedildi']]
                    ]
                ],
                '/api/users/{id}/jobs' => [
                    'get' => [
                        'tags'       => ['JobPostings'],
                        'summary'    => 'Mezunun yayınladığı ilanlar (Nested Rota) - ApiJobController::userJobs',
                        'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                        'responses'  => ['200' => ['description' => 'Mezunun ilanları']]
                    ]
                ],
                '/api/applications' => [
                    'get' => [
                        'tags'      => ['JobApplications'],
                        'summary'   => 'Tüm başvuruları listele (Doğrudan Rota) - ApiApplicationController::index',
                        'responses' => ['200' => ['description' => 'Başvurular listesi']]
                    ]
                ]
            ]
        ], 200);
    }
}
