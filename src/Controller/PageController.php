<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Combo;
use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\Restaurant;
use App\Service\ImageService;
use App\Service\PagesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    const ALLOWED_DOMS = '<p><b><strong><a><i><br><u>';
    const KEY_FOR_LIST = 'elements';
    const baseUrl = '/restaurant/';
//    const baseUrl = 'https://restos.wichisoft.com.ar/restaurant/';

    public function __construct()
    {
    }

    private function getCookie($key)
    {
        if (isset($_COOKIE[$key]))
        {
            return $_COOKIE[$key];
        }

        return false;
    }

    /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showCategories(PagesService $pagesService, Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->getEnabledCategoriesForRestaurant($this->getUser()->getRestaurantId());

        $categoriesArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->render('pages/page_details.html.twig', [
            'pageName' => 'Categorias',
            'itemTitle' => 'Categoria',
            'route' => $request->get('_route'),
            'categories' => $categoriesArray,
            'user' => DashboardController::$user,
        ]);
    }

    /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showCombos(PagesService $pagesService, Request $request)
    {
        $combos = $this->getDoctrine()->getRepository(Combo::class)
            ->getEnabledCombosForRestaurant($this->getUser()->getRestaurantId());

        $combosArray = [];
        /** @var Combo $combo */
        foreach ($combos as $combo) {
            $combosArray[] = [
                'id' => $combo->getId(),
                'name' => $combo->getName(),
                'description' => $combo->getDescription(),
                'imageUrl' => $combo->getImageUrl(),
                'price' => $combo->getPrice(),
            ];
        }

        return $this->render('pages/page_details_combos.html.twig', [
            'pageName' => 'Combos',
            'route' => $request->get('_route'),
            'combos' => $combosArray,
            'user' => DashboardController::$user,
        ]);
    }

   /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showRestaurants(PagesService $pagesService, Request $request)
    {
        $restaurants = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findBy(['enabled' => 1, 'id' => $this->getUser()->getRestaurantId()], ['id' => 'ASC']);

        $restaurantsArray = [];
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant) {
            $restaurantsArray[] = [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'qrUrl' => $restaurant->getQrUrl(),
                'logo' => $restaurant->getLogoUrl(),
                'link' => self::baseUrl.$restaurant->getSlug(),
            ];
        }

        return $this->render('pages/page_details_restaurant.html.twig', [
            'pageName' => 'Restaurant',
            'itemTitle' => 'Restaurant',
            'route' => $request->get('_route'),
            'restaurants' => $restaurantsArray,
            'user' => DashboardController::$user,
        ]);
    }

    /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showDishes(PagesService $pagesService, Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(
                ['enabled' => 1, 'restaurantId' => $this->getUser()->getRestaurantId()],
                ['id' => 'ASC']
            );

        $returnArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $dishes = $this->getDoctrine()->getRepository(Dish::class)
                ->findBy(['enabled' => 1, 'categoryId' => $category->getId()]);

            $dishesArray = [];
            /** @var Dish $dish */
            foreach ($dishes as $dish) {
                $dishesArray[] = [
                    'id' => $dish->getId(),
                    'name' => $dish->getName(),
                    'description' => $dish->getDescription(),
                    'price' => $dish->getPrice(),
                    'currency' => $dish->getCurrency()->getSymbol(),
                    //'image' => 'xxx',
                ];
            }

            $returnArray[] = [
                'name' => $category->getName(),
                'dishes' => $dishesArray,
            ];
        }

        return $this->render('pages/page_details_dishes.html.twig', [
            'pageName' => 'Platos',
            'route' => $request->get('_route'),
            'categories' => $returnArray,
            'user' => DashboardController::$user,
        ]);
    }

    /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showDishesCombos(PagesService $pagesService, Request $request)
    {
        $combos = $this->getDoctrine()->getRepository(Combo::class)
            ->findBy(
                ['enabled' => 1, 'restaurantId' => $this->getUser()->getRestaurantId()],
                ['id' => 'ASC']
            );

        $returnArray = [];
        /** @var Combo $combo */
        foreach ($combos as $combo) {
            $comboDishes = $this->getDoctrine()->getRepository(ComboDish::class)
                ->findBy(['comboId' => $combo->getId()],['dishId' => 'ASC']);

            $dishesArray = [];
            /** @var ComboDish $comboDish */
            foreach ($comboDishes as $comboDish) {
                $dishes = $this->getDoctrine()->getRepository(Dish::class)
                    ->findBy(['id' => $comboDish->getDishId(), 'enabled' => 1]);

                if (null !== $dishes) {
                    foreach ($dishes as $dish) {
                        $dishesArray[] = [
                            'id' => $dish->getId(),
                            'name' => $dish->getName(),
                            'description' => $dish->getDescription(),
                        ];
                    }
                }
            }

            $returnArray[] = [
                'id' => $combo->getId(),
                'name' => $combo->getName(),
                'description' => $combo->getDescription(),
                'price' => $combo->getPrice(),
                'currency' => $combo->getCurrency()->getSymbol(),
                'dishes' => $dishesArray,
            ];
        }

        return $this->render('pages/page_details_dishes_combo.html.twig', [
            'pageName' => 'Platos en combos',
            'route' => $request->get('_route'),
            'categories' => $returnArray,
            'user' => DashboardController::$user,
        ]);
    }
}
