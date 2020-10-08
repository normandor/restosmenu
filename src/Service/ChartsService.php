<?php

namespace App\Service;

use App\Entity\PageVisits;
use Doctrine\ORM\EntityManagerInterface;

class ChartsService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return json
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLineTotalData()
    {
        $conn = $this->entityManager->getConnection();

        $yAxisTitleTot = 'Visitas';
        $seriesNameTot = 'Global';
        //        $seriesColorTot = ['#C0392B', '#8E44AD', '#2E86C1', '#17A589', '#D4AC0D', '#CA6F1E', '#839192'];
        $seriesColorTot = '#CA6F1E';
        $seriesAreaColorTot = '#FAD7A0';
        $dataLegendTot = 'Global';
        $dateArrayTot = $this->getMonthsXAxis();
        $dataRow = [];
        for ($i = 0, $iMax = count($dateArrayTot); $i < $iMax; $i++) {
            $query = "SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,7) = :laDate";
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
     * @return json
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLineTotalDataForRestaurant($restaurantId)
    {
        $conn = $this->entityManager->getConnection();

        $yAxisTitleTot = 'Visitas';
        $seriesNameTot = 'Global';
        $seriesColorTot = '#CA6F1E';
        $seriesAreaColorTot = '#FAD7A0';
        $dataLegendTot = 'Global';
        $dateArrayTot = $this->getMonthsXAxis();
        $dataRow = [];
        for ($i = 0, $iMax = count($dateArrayTot); $i < $iMax; $i++) {
            $query = "SELECT COUNT(*) as cnt FROM page_visits WHERE LEFT(datetime,7) = :laDate AND restaurant_id = :restaurant_id";
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
     * @return json
     */
    public function getLineDataPerRestaurant()
    {
        $yAxisTitleTot = 'Visitas';
        $seriesNames = [];
        $seriesColorTot = ['#C0392B', '#8E44AD', '#2E86C1', '#17A589', '#D4AC0D', '#CA6F1E', '#839192'];
        $seriesAreaColorTot = ['#C0392B', '#8E44AD', '#2E86C1', '#17A589', '#D4AC0D', '#CA6F1E', '#839192'];
        $dataLegendTot = 'Global';
        $dateArrayTot = $this->getMonthsXAxis();

        $pageVisitsRepository = $this->entityManager->getRepository(PageVisits::class);
        $restaurantsArray = $pageVisitsRepository->getAllRestaurants();

        $dataRowArray = [];
        foreach ($restaurantsArray as $restaurant) {
            $seriesNames[] = $restaurant['name'];
            $dataRow = [];
            foreach ($dateArrayTot as $yearMonth) {
                $dataRow[] = $pageVisitsRepository->getCountByRestaurantIdAndYearMonth($restaurant['id'], $yearMonth);
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
}
