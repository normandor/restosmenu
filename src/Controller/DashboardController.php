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

        if (in_array('ROLE_VIEW_GRAPHS', $roles, true)) {
            $charts = [
                [
                    'divId' => 'chartTotalVisits',
                    'title' => 'dashboard.visits_to_menu',
                ],
                [
                    'divId' => 'chartVisitsPerRestaurant',
                    'title' => 'dashboard.visits_per_restaurant',
                ],
                [
                    'divId' => 'chartTotalVisitsByDay',
                    'title' => 'dashboard.visits_to_menu',
                ],
                [
                    'divId' => 'chartVisitsPerRestaurantByDay',
                    'title' => 'dashboard.visits_per_restaurant',
                ],
            ];
        } else {
            $charts = [
                [
                    'divId' => 'chartTotalRestaurant',
                    'title' => 'dashboard.visits_to_menu',
                ],
                [
                    'divId' => 'chartTotalRestaurantByDay',
                    'title' => 'dashboard.visits_to_menu',
                ],
            ];
        }

        $lines = [
            [
                'title' => 'dashboard.visits_to_site',
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
