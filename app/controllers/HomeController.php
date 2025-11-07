<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View\ViewRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function __construct(private readonly ViewRenderer $view)
    {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'home', [
            'pageTitle' => 'dashboard.title',
            'theme' => $request->getAttribute('theme', 'system'),
            'chunks' => [
                [
                    'title' => 'dashboard.quick_actions',
                    'items' => [
                        ['label' => 'dashboard.log_time', 'href' => '#'],
                        ['label' => 'dashboard.submit_hours', 'href' => '#'],
                    ],
                ],
                [
                    'title' => 'dashboard.guides_title',
                    'items' => [
                        ['label' => 'dashboard.guide_how_to', 'href' => '#'],
                        ['label' => 'dashboard.guide_psl', 'href' => '#'],
                    ],
                ],
            ],
        ]);
    }
}
