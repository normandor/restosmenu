<?php

namespace App\Service;

use App\Controller\ChartDataController;
use App\Entity\PageVisits;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

// TODO: remove duplicate code in these calls
class ChartsService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $unit
     *
     * @return json
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLineTotalData(string $unit)
    {
        $conn = $this->entityManager->getConnection();

        $yAxisTitleTot = 'Visitas';
        $seriesNameTot = 'Global';
        $seriesColorTot = '#CA6F1E';
        $seriesAreaColorTot = '#FAD7A0';
        $dataLegendTot = 'Global';

        if (ChartDataController::UNIT_MES === $unit) {
            $dateArrayTot = $this->getMonthsXAxis();
        } else {
            $today = new \DateTime();
            $tomorrow = new \DateTime();
            $tomorrow->modify('-6 months');

            $dateArrayTot = $this->getDatesFromRange($tomorrow->format('Y-m-d'), $today->format('Y-m-d'));
        }
        $dataRow = [];
        for ($i = 0, $iMax = count($dateArrayTot); $i < $iMax; $i++) {
            if (ChartDataController::UNIT_MES === $unit) {
                $query = 'SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,7) = :laDate';
            } else {
                $query = 'SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,10) = :laDate';
            }

            $stmt = $conn->prepare($query);
            $stmt->execute(['laDate' => $dateArrayTot[$i]]);
            $stmt->execute();
            $row = $stmt->fetch();
            $dataRow[] = $row['cnt'];
        }

        return json_encode([
            'dateTot' => $dateArrayTot,
            'dataLegendTot' => $dataLegendTot,
            'yAxisTitleTot' => $yAxisTitleTot,
            'seriesNameTot' => $seriesNameTot,
            'seriesLineColorTot' => $seriesColorTot,
            'seriesAreaColorTot' => $seriesAreaColorTot,
            'seriesDataTot' => $dataRow,
        ]);
    }

    /**
     * @param int    $restaurantId
     * @param string $unit
     *
     * @return json
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLineTotalDataForRestaurant($restaurantId, $unit)
    {
        $conn = $this->entityManager->getConnection();

        $yAxisTitleTot = 'Visitas';
        $seriesNameTot = 'Global';
        $seriesColorTot = '#CA6F1E';
        $seriesAreaColorTot = '#FAD7A0';
        $dataLegendTot = 'Global';

        if (ChartDataController::UNIT_MES === $unit) {
            $dateArrayTot = $this->getMonthsXAxis();
        } else {
            $today = new \DateTime();
            $tomorrow = new \DateTime();
            $tomorrow->modify('-6 months');

            $dateArrayTot = $this->getDatesFromRange($tomorrow->format('Y-m-d'), $today->format('Y-m-d'));
        }

        $dataRow = [];
        for ($i = 0, $iMax = count($dateArrayTot); $i < $iMax; $i++) {
            if (ChartDataController::UNIT_MES === $unit) {
                $query = 'SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,7) = :laDate AND restaurant_id = :restaurant_id';
            } else {
                $query = 'SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,10) = :laDate AND restaurant_id = :restaurant_id';
            }

            $stmt = $conn->prepare($query);
            $stmt->execute(['laDate' => $dateArrayTot[$i], 'restaurant_id' => $restaurantId]);
            $stmt->execute();
            $row = $stmt->fetch();
            $dataRow[] = $row['cnt'];
        }

        return json_encode([
            'dateTot' => $dateArrayTot,
            'dataLegendTot' => $dataLegendTot,
            'yAxisTitleTot' => $yAxisTitleTot,
            'seriesNameTot' => $seriesNameTot,
            'seriesLineColorTot' => $seriesColorTot,
            'seriesAreaColorTot' => $seriesAreaColorTot,
            'seriesDataTot' => $dataRow,
        ]);
    }

    /**
     * @param string $unit
     *
     * @return json
     */
    public function getLineDataPerRestaurant($unit)
    {
        $yAxisTitleTot = 'Visitas';
        $seriesNames = [];
        $seriesColorTot = ['#C0392B', '#8E44AD', '#2E86C1', '#17A589', '#D4AC0D', '#CA6F1E', '#839192'];
        $seriesAreaColorTot = ['#C0392B', '#8E44AD', '#2E86C1', '#17A589', '#D4AC0D', '#CA6F1E', '#839192'];
        $dataLegendTot = 'Global';
        if (ChartDataController::UNIT_MES === $unit) {
            $dateArrayTot = $this->getMonthsXAxis();
        } else {
            $today = new \DateTime();
            $tomorrow = new \DateTime();
            $tomorrow->modify('-6 months');

            $dateArrayTot = $this->getDatesFromRange($tomorrow->format('Y-m-d'), $today->format('Y-m-d'));
        }

        $pageVisitsRepository = $this->entityManager->getRepository(PageVisits::class);
        $restaurantsArray = $pageVisitsRepository->getAllRestaurants();

        $dataRowArray = [];
        foreach ($restaurantsArray as $restaurant) {
            $seriesNames[] = $restaurant['name'];
            $dataRow = [];
            foreach ($dateArrayTot as $yearMonth) {
                if (ChartDataController::UNIT_MES === $unit) {
                    $dataRow[] = $pageVisitsRepository->getCountByRestaurantIdAndYearMonth($restaurant['id'], $yearMonth);
                } else {
                    $dataRow[] = $pageVisitsRepository->getCountByRestaurantIdAndDay($restaurant['id'], $yearMonth);
                }
            }

            $dataRowArray[] = $dataRow;
        }

        return json_encode([
            'dateTot' => $dateArrayTot,
            'dataLegendTot' => $dataLegendTot,
            'yAxisTitleTot' => $yAxisTitleTot,
            'seriesNameTot' => $seriesNames,
            'seriesLineColorTot' => $seriesColorTot,
            'seriesAreaColorTot' => $seriesAreaColorTot,
            'seriesDataTot' => $dataRowArray,
        ]);
    }

    /**
     *
     * @param int|null $nbOfMonths
     *
     * @return array
     */
    private function getMonthsXAxis($nbOfMonths = 12)
    {
        $todaysYear = date('Y');
        $todaysMonth = date('m');
        $months = [];

        for ($i = 0; $i < $nbOfMonths; $i++) {
            $months[] = $todaysYear.'-'.str_pad($todaysMonth, 2, '0', STR_PAD_LEFT);

            $todaysMonth--;
            if ($todaysMonth === 0) {
                $todaysMonth = 12;
                $todaysYear--;
            }
        }

        return array_reverse($months);
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $format
     *
     * @return array
     * @throws \Exception
     */
    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();

        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }
}
