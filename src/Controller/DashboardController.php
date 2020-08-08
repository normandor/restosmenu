<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    public static $user;

    public function index(Request $request)
    {
        $user = $this->getUser()->getUsername();
        $roles = $this->getUser()->getRoles();
        $lines = [
            [
                'title' => 'Visitas / Comentarios',
                'charts' => [
                    [
                        'divId' => 'chartTotalAnomalias',
                        'title' => 'Cantidad de visitas al sitio',
                    ],
                    [
                        'divId' => 'chartPorZonaAnomalias',
                        'title' => 'Cantidad de comentarios',
                    ],
                ]
            ],
        ];

        return $this->render('index.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'parent' => 0,
            'lines' => $lines,
            'route' => $request->get('_route'),
        ]);
    }
}
