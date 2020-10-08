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

        if(in_array('ROLE_VIEW_GRAPHS', $roles, true)) {
            $charts = [
                [
                    'divId' => 'chartTotalAnomalias',
                    'title' => 'Cantidad de visitas a los menús',
                ],
                [
                    'divId' => 'chartVisitsPerRestaurant',
                    'title' => 'Cantidad de visitas por restaurant',
                ],
            ];
        } else {
            $charts = [
                [
                    'divId' => 'chartTotalRestaurant',
                    'title' => 'Cantidad de visitas al menú',
                ],
            ];
        }

        $lines = [
            [
                'title' => 'Visitas al sitio',
                'charts' => $charts
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
