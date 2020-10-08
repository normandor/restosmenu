<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ChartsService;


class ChartDataController extends AbstractController
{
    public function index($id)
    {
        return new Response('Unrecognized call');
    }

    public function getLineTotalDataForRestaurant(ChartsService $chartsService)
    {
        $json = $chartsService->getLineTotalDataForRestaurant($this->getUser()->getRestaurantId());

        return new Response($json);
    }

    public function getLineTotalData(ChartsService $chartsService)
    {
        if (!in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineTotalData();

        return new Response($json);
    }

    public function getLineDataPerRestaurant(ChartsService $chartsService)
    {
        if (!in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineDataPerRestaurant();

        return new Response($json);
    }
}
