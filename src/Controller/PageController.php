<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\Restaurant;
use App\Entity\SettingsImage;
use App\Entity\SettingsPage;
use App\Service\PagesService;
use App\Service\SettingsImageService;
use App\Service\SettingsPageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    const baseUrl = '/restaurant/';
    const absoluteUrl = 'https://restos.wichisoft.com.ar/restaurant/';

    const CATEGORY_BASICO = 'basico';
    const CATEGORY_COMBO = 'combo';
    const CATEGORY_TEXT = 'text';
    const CATEGORY_IMAGE = 'image';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showCategories(Request $request)
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
                'enabled' => $category->getEnabled(),
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
     * @param Request $request
     *
     * @return Response
     */
    public function showCombos(Request $request)
    {
        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => self::CATEGORY_COMBO,
            ]);

        $combosArray = [];
        /** @var Category $combo */
        foreach ($combos as $combo) {
            $combosArray[] = [
                'id' => $combo->getId(),
                'name' => $combo->getName(),
                'description' => $combo->getDescription(),
                'imageUrl' => $combo->getImageUrl(),
                'price' => $combo->getPrice(),
                'currency' => $combo->getCurrency()->getSymbol(),
                'enabled' => $combo->getEnabled(),
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
     * @param Request $request
     *
     * @return Response
     */
    public function showRestaurants(Request $request)
    {
        $restaurants = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findBy(['enabled' => 1, 'id' => $this->getUser()->getRestaurantId()], ['id' => 'ASC']);

        $restaurantsArray = [];
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant) {
            /** @var Category $category */
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(
                [
                    'restaurantId' => $restaurant->getId(),
                    'categoryType' => RestaurantController::LOGO_CATEGORY_TYPE,
                    'name' => RestaurantController::LOGO_CATEGORY_NAME,
                ]
            );

            $restaurantsArray[] = [
                'id' => $restaurant->getId(),
                'name' => $restaurant->getName(),
                'qrUrl' => $restaurant->getQrUrl(),
                'logo' => $category->getImageUrl(),
                'link' => self::baseUrl.$restaurant->getSlug(),
                'deeplink' => self::absoluteUrl.$restaurant->getSlug(),
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
    public function showPageSettings(
        SettingsPageService $settingsPageService,
        SettingsImageService $settingsImageService,
        Request $request
    ) {
        $settingsPage = $settingsPageService->getPropertiesByRestaurantId($this->getUser()->getRestaurantId());
        $settingsImage = $settingsImageService->getPropertiesByRestaurantId($this->getUser()->getRestaurantId());

        return $this->render('pages/page_details_page_settings.html.twig', [
            'pageName' => 'Restaurant',
            'itemTitle' => 'Restaurant',
            'route' => $request->get('_route'),
            'user' => DashboardController::$user,
            'settingsPage' => $settingsPage,
            'settingsImage' => $settingsImage,
            'select' => [
                'font_options' => $this->getAvailableFontSizes(),
                'width_options' => $this->getAvailableWidthOptions(),
            ],
        ]);
    }

    /**
     * @return array
     */
    private function getAvailableFontSizes(): array
    {
        $sizes = [];

        for ($i = 18; $i <= 40; $i += 2) {
            $sizes[] = $i.'px';
        }

        return $sizes;
    }

    /**
     * @return array
     */
    private function getAvailableWidthOptions(): array
    {
        $widths = [];

        for ($i = 20; $i <= 100; $i += 5) {
            $widths[] = $i.'%';
        }

        return $widths;
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
            'fonts' => $fonts,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showPageOrder(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(['restaurantId' => $this->getUser()->getRestaurantId()],['orderShow' => 'ASC']);

        $categoriesArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            if (PageController::CATEGORY_IMAGE == $category->getCategoryType()) {
                /** @var SettingsImage $settingsImage */
                $settingsImage = $this->getDoctrine()->getRepository(SettingsImage::class)->findOneBy(
                    [
                        'key' => $category->getName(),
                        'restaurantId' => $this->getUser()->getRestaurantId(),
                        'property' => 'visible',
                    ]
                );
                $visible = $settingsImage->getValue() === 'true';
            }

            $categoriesArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'type' => 'category_type.'.$category->getCategoryType(),
                'order' => $category->getOrderShow(),
                'enabled' =>
                    (PageController::CATEGORY_IMAGE == $category->getCategoryType())
                    ? $visible
                    : $category->getEnabled(),
            ];
        }

        return $this->render('pages/page_details_page_order.html.twig', [
            'pageName' => 'order_of_categories',
            'subtitle' => 'order_of_categories_and_images',
            'itemTitle' => 'Categoría',
            'route' => $request->get('_route'),
            'user' => DashboardController::$user,
            'categories' => $categoriesArray,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showDishes(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(
                ['restaurantId' => $this->getUser()->getRestaurantId(), 'categoryType' => self::CATEGORY_BASICO],
                ['orderShow' => 'ASC']
            );

        $returnArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $dishes = $this->getDoctrine()->getRepository(Dish::class)
                ->findBy(
                    ['categoryId' => $category->getId()],
                    ['orderShow' => 'ASC']
                );

            $dishesArray = [];
            /** @var Dish $dish */
            foreach ($dishes as $dish) {
                $dishesArray[] = [
                    'id' => $dish->getId(),
                    'name' => $dish->getName(),
                    'description' => $dish->getDescription(),
                    'price' => $dish->getPrice(),
                    'currency' => $dish->getCurrency()->getSymbol(),
                    'enabled' => $dish->getEnabled(),
                    'order' => $dish->getOrderShow(),
                    'image' => $dish->getImageUrl(),
                ];
            }

            $returnArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'enabled' => $category->getEnabled(),
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
     * @param Request $request
     *
     * @return Response
     */
    public function showDishesCombos(Request $request)
    {
        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(
                [
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_COMBO,
                ],
                ['orderShow' => 'ASC']
            );

        $returnArray = [];
        /** @var Category $combo */
        foreach ($combos as $combo) {
            $comboDishes = $this->getDoctrine()->getRepository(ComboDish::class)
                ->findBy(
                    ['comboId' => $combo->getId()],
                    ['orderShow' => 'ASC']
                );

            $dishesArray = [];
            /** @var ComboDish $comboDish */
            foreach ($comboDishes as $comboDish) {
                $dishes = $this->getDoctrine()->getRepository(Dish::class)
                    ->findBy(['id' => $comboDish->getDishId()]);

                if (null !== $dishes) {
                    foreach ($dishes as $dish) {
                        $dishesArray[] = [
                            'id' => $dish->getId(),
                            'name' => $dish->getName(),
                            'order' => $comboDish->geOrderShow(),
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
                'enabled' => $combo->getEnabled(),
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
