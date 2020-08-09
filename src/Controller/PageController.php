<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Combo;
use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\Restaurant;
use App\Entity\SettingsPage;
use App\Service\ImageService;
use App\Service\PagesService;
use App\Service\SettingsPageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    const ALLOWED_DOMS = '<p><b><strong><a><i><br><u>';
    const KEY_FOR_LIST = 'elements';
    const baseUrl = '/restaurant/';

    const CATEGORY_BASICO = 'basico';
    const CATEGORY_COMBO = 'combo';
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
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => self::CATEGORY_BASICO,
            ]);

        $categoriesArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->render('pages/page_details_categories.html.twig', [
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
        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => self::CATEGORY_COMBO,
            ]);

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
     * @param PagesService $settingsPageService
     * @param Request      $request
     *
     * @return Response
     */
    public function showPageSettings(SettingsPageService $settingsPageService, Request $request)
    {
        $settings = $settingsPageService->getPropertiesByRestaurantId($this->getUser()->getRestaurantId());

        return $this->render('pages/page_details_page_settings.html.twig', [
            'pageName' => 'Restaurant',
            'itemTitle' => 'Restaurant',
            'route' => $request->get('_route'),
            'user' => DashboardController::$user,
            'settings' => $settings,
            'select' => [
                'font_options' => [
                    '20px',
                    '30px',
                    '40px',
                    '50px',
                ],
            ],
        ]);
    }

    /**
     * @param string $key
     *
     * @return Response
     */
    public function showSelectFont($key)
    {
        /** @var SettingsPage $selectedFont */
        $selectedFont = $this->getDoctrine()->getRepository(SettingsPage::class)
            ->findOneBy(['key' => $key, 'property' => 'font-family', 'restaurantId' => $this->getUser()->getRestaurantId()]);

        $fonts = [
            '"Times New Roman", Times, serif',
            'Arial, Helvetica, sans-serif',
            '"Arial Black", Gadget, sans-serif',
            '"Comic Sans MS", cursive, sans-serif',
            'Impact, Charcoal, sans-serif',
            '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
            '"Courier New", Courier, monospace',
            '"Lucida Console", Monaco, monospace',
        ];

        return $this->render('user/modals/modal_select_font.twig', [
            'title' => 'select_font.title',
            'subtitle' => 'select_font.select_font',
            'present' => 'select_font.present',
            'selected' => ($selectedFont) ? $selectedFont->getValue() : '',
            'fonts' => $fonts
        ]);
    }

    /**
     * @param PagesService $pagesService
     * @param Request      $request
     *
     * @return Response
     */
    public function showPageOrder(PagesService $pagesService, Request $request)
    {
        $settings = [
            [
                'key' => 'menu.body',
                'name' => 'Cuerpo de la pagina',
                'font-family' => '',
                'font-size' => '',
                'color' => '',
                'background-color' => 'brown;',
            ],
            [
                'key' => 'menu.restaurant.title',
                'name' => 'Nombre del restaurant',
                'font-family' => '"Comic Sans MS", cursive, sans-serif;',
                'font-size' => '20px;',
                'color' => 'red;',
                'background-color' => '',
            ],
        ];

        return $this->render('pages/page_details_page_order.html.twig', [
            'pageName' => 'Restaurant',
            'itemTitle' => 'Restaurant',
            'route' => $request->get('_route'),
            'user' => DashboardController::$user,
            'settings' => $settings,
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
        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_COMBO,
            ]);

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
