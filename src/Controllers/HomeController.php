<?php
namespace App\Controllers;

use App\Core\Response;
use App\Core\View;

/**
 * HomeController (MVC - Controller Katmanı)
 * Web sayfalarını ve temel GET rotalarını yönetir.
 */
class HomeController
{
    /**
     * GET / - Geçici Ana Sayfa (Control Panel)
     */
    public function index(): void
    {
        View::render('home');
    }

    /**
     * GET /Alumni - "ok" yanıtı
     */
    public function alumni(): void
    {
        Response::text('ok');
    }

    /**
     * GET /hello - "Hello World"
     */
    public function hello(): void
    {
        Response::text('Hello World');
    }

    /**
     * GET /hello/{name} - "Hello {name}!"
     */
    public function helloName(string $name): void
    {
        Response::text('Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '!');
    }

    /**
     * GET /sum/{a}/{b} - İki sayının toplamı
     */
    public function sum(string $a, string $b): void
    {
        if (is_numeric($a) && is_numeric($b)) {
            Response::text((string)($a + $b));
        } else {
            Response::text('Hata: Geçerli iki sayı giriniz.', 400);
        }
    }

    /**
     * GET /about - Geçici Hakkında Sayfası
     */
    public function about(): void
    {
        View::render('about');
    }
}
