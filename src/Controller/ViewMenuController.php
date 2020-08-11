<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Restaurant;
use App\Service\DishService;
use App\Service\SettingsPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ViewMenuController extends AbstractController
{
    private $dishService;

    public function __construct(DishService $dishService)
    {
        $this->dishService = $dishService;
    }

    public function showPreview(
        Request $request,
        SettingsPageService $settingsPageService,
        $page
    ) {
        $restaurantId = $this->getUser()->getRestaurantId();
        $restaurant = $this->getRestaurantByRestaurantId($restaurantId);
        $restoMenu = $this->getRestoMenuByRestaurantId($restaurantId);

        $properties = $settingsPageService->getPropertiesByRestaurantId($restaurantId);

        return $this->render('view_menu_'.$page.'.html.twig', [
            'route' => $request->get('_route'),
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $restaurant->getLogoUrl(),
            'properties' => $properties,
            'restoMenu' => $restoMenu,
        ]);
    }

    public function showMenu(
        SettingsPageService $settingsPageService,
        $restaurantSlug
    ) {
        if (0 === $restaurantSlug) {
            throw new NotFoundHttpException('Sorry, not existing!');
        }

        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findOneBy(['slug' => $restaurantSlug]);

        if (!$restaurant) {
            throw new NotFoundHttpException('Restaurant not found');
        }

        $restoMenu = $this->getRestoMenuByRestaurantId($restaurant->getId());
        $properties = $settingsPageService->getPropertiesByRestaurantId($restaurant->getId());

        return $this->render('view_menu_1_public.html.twig', [
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $restaurant->getLogoUrl(),
            'properties' => $properties,
            'restoMenu' => $restoMenu,
        ]);
    }

    /**
     * @param int $restaurantId
     *
     * @return Restaurant
     */
    private function getRestaurantByRestaurantId(int $restaurantId) : Restaurant
    {
        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findOneBy(['selected' => 1, 'id' => $restaurantId]);

        return $restaurant;
    }

    /**
     * @param int $restaurantId
     *
     * @return array
     */
    private function getRestoMenuByRestaurantId(int $restaurantId) : array
    {
        $restoMenu = [];

        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(
                [
                    'restaurantId' => $restaurantId,
                    'enabled' => 1,
                ],
                ['orderShow' => 'ASC']
            );

        /** @var Category $category */
        foreach ($categories as $category) {
            if (PageController::CATEGORY_BASICO === $category->getCategoryType()) {
                $restoMenu[] = [
                    'categoria' => $category->getName(),
                    'class' => 'menu_category',
                    'items' => $this->dishService->getDishesByCategoryId($category->getId()),
                ];
            }
            if (PageController::CATEGORY_COMBO === $category->getCategoryType()) {
                $restoMenu[] = [
                    'categoria' => $category->getName(),
                    'description' => $category->getDescription(),
                    'class' => 'menu_promo_title',
                    'price' => $category->getPrice(),
                    'currency' => $category->getCurrency()->getSymbol(),
                    'items' => $this->dishService->getDishesByComboId($category->getId()),
                ];
            }
        }

        return $restoMenu;
    }
}
