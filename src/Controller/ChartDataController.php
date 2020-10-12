<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ChartsService;


class ChartDataController extends AbstractController
{
    const UNIT_MES = 'm';
    const UNIT_DAY = 'd';

    public function index($id)
    {
        return new Response('Unrecognized call');
    }

    public function getLineTotalDataForRestaurant(ChartsService $chartsService)
    {
        $json = $chartsService->getLineTotalDataForRestaurant($this->getUser()->getRestaurantId(), self::UNIT_MES);

        return new Response($json);
    }

    public function getLineTotalDataForRestaurantByDay(ChartsService $chartsService)
    {
        $json = $chartsService->getLineTotalDataForRestaurant($this->getUser()->getRestaurantId(), self::UNIT_DAY);

        return new Response($json);
    }

    public function getLineTotalData(ChartsService $chartsService)
    {
        if (!in_array('ROLE_VIEW_GRAPHS', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineTotalData(self::UNIT_MES);

        return new Response($json);
    }

    public function getLineTotalDataByDay(ChartsService $chartsService)
    {
        if (!in_array('ROLE_VIEW_GRAPHS', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineTotalData(self::UNIT_DAY);

        return new Response($json);
    }

    public function getLineDataPerRestaurant(ChartsService $chartsService)
    {
        if (!in_array('ROLE_VIEW_GRAPHS', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineDataPerRestaurant(self::UNIT_MES);

        return new Response($json);
    }

    public function getLineDataPerRestaurantByDay(ChartsService $chartsService)
    {
        if (!in_array('ROLE_VIEW_GRAPHS', $this->getUser()->getRoles(), true)) {
            return new Response([]);
        }

        $json = $chartsService->getLineDataPerRestaurant(self::UNIT_DAY);

        return new Response($json);
    }
}
