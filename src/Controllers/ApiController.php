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
                'description' => 'YBSB3001 Web Programming - MVC Architecture & REST Endpoints',
                'version'     => '1.0.0'
            ],
            'servers' => [
                ['url' => '/', 'description' => 'Mevcut Sunucu']
            ],
            'paths' => [
                '/api/health' => [
                    'get' => [
                        'summary'   => 'Sistem sağlık kontrolü',
                        'responses' => [
                            '200' => ['description' => 'Sistem çalışıyor']
                        ]
                    ]
                ],
                '/api/users' => [
                    'get' => [
                        'summary'   => 'Tüm kullanıcıları listele (Read All)',
                        'responses' => [
                            '200' => ['description' => 'Kullanıcı listesi']
                        ]
                    ],
                    'post' => [
                        'summary'     => 'Yeni kullanıcı oluştur (Create)',
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
                            '201' => ['description' => 'Kullanıcı başarıyla oluşturuldu'],
                            '400' => ['description' => 'Geçersiz istek']
                        ]
                    ]
                ],
                '/api/users/{id}' => [
                    'get' => [
                        'summary'    => 'ID ile tek kullanıcı getir (Read One)',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Kullanıcı bulundu'],
                            '404' => ['description' => 'Kullanıcı bulunamadı']
                        ]
                    ],
                    'put' => [
                        'summary'    => 'Kullanıcıyı tamamen güncelle (Full Update)',
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
                            '200' => ['description' => 'Kullanıcı güncellendi'],
                            '404' => ['description' => 'Kullanıcı bulunamadı']
                        ]
                    ],
                    'patch' => [
                        'summary'    => 'Kullanıcı alanını kısmen güncelle (Partial Update)',
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
                                            'company' => ['type' => 'string', 'example' => 'Yeni Şirket A.Ş.']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Kullanıcı güncellendi'],
                            '404' => ['description' => 'Kullanıcı bulunamadı']
                        ]
                    ],
                    'delete' => [
                        'summary'    => 'Kullanıcıyı sil (Delete)',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                        ],
                        'responses' => [
                            '200' => ['description' => 'Kullanıcı silindi'],
                            '404' => ['description' => 'Kullanıcı bulunamadı']
                        ]
                    ]
                ]
            ]
        ], 200);
    }
}
