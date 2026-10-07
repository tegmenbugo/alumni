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
     * GET /api/openapi.json - OpenAPI 3.0 Şeması
     */
    public function openapi(): void
    {
        Response::json([
            'openapi' => '3.0.0',
            'info' => [
                'title'       => 'Alumni Tracking System API',
                'description' => "YBSB3001 Web Programming - MVC Architecture & REST Endpoints.\n\n• REST API uç noktaları ApiUserController tarafından yönetilmektedir.\n• Web HTML arayüzü rotaları (/users) UserController tarafından yönetilmektedir.\n• Swagger/OpenAPI şeması her rota ve controller değişikliğinde sürekli güncel tutulur.",
                'version'     => '1.0.0'
            ],
            'tags' => [
                [
                    'name'        => 'ApiUserController',
                    'description' => 'Mezun REST API CRUD operasyonları (JSON girdi / JSON çıktı)'
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
                ]
            ]
        ], 200);
    }
}
