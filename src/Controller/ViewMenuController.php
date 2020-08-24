<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Service\DishService;
use App\Service\SettingsImageService;
use App\Service\SettingsPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ViewMenuController extends AbstractController
{
    private $dishService;
    private $restaurantRepository;

    public const classMapping = [
        'restaurant_name' => 'menu_restaurant_title',
        'restaurant_logo' => 'restaurant_logo',
    ];

    public function __construct(DishService $dishService, RestaurantRepository $restaurantRepository)
    {
        $this->dishService = $dishService;
        $this->restaurantRepository = $restaurantRepository;
    }

    public function showPreview(
        Request $request,
        SettingsPageService $settingsPageService,
        SettingsImageService $settingsImageService,
        bool $mobile
    ) {
        $restaurantId = $this->getUser()->getRestaurantId();
        $restaurant = $this->getRestaurantByRestaurantId($restaurantId);
        $restoMenu = $this->getRestoMenuByRestaurantId($restaurantId);

        $properties = $settingsPageService->getPropertiesByRestaurantId($restaurantId);
        $imageProperties = $settingsImageService->getPropertiesByRestaurantId($restaurantId);

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(
            [
                'restaurantId' => $restaurantId,
                'categoryType' => RestaurantController::LOGO_CATEGORY_TYPE,
                'name' => RestaurantController::LOGO_CATEGORY_NAME,
            ]
        );

        return $this->render('view_menu_1_preview.html.twig', [
            'route' => $request->get('_route'),
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $category->getImageUrl(),
            'properties' => $properties,
            'imageProperties' => $imageProperties,
            'restoMenu' => $restoMenu,
            'forceMobileWidth' => $mobile,
        ]);
    }

    public function showMenu(
        SettingsPageService $settingsPageService,
        SettingsImageService $settingsImageService,
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
        $imageProperties = $settingsImageService->getPropertiesByRestaurantId($restaurant->getId());

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(
            [
                'restaurantId' => $restaurant->getId(),
                'categoryType' => RestaurantController::LOGO_CATEGORY_TYPE,
                'name' => RestaurantController::LOGO_CATEGORY_NAME,
            ]
        );

        return $this->render('view_menu_1_public.html.twig', [
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $category->getImageUrl(),
            'properties' => $properties,
            'imageProperties' => $imageProperties,
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
                    'category' => $category->getName(),
                    'category_type' => $category->getCategoryType(),
                    'class' => 'menu_category',
                    'items' => $this->dishService->getDishesByCategoryId($category->getId()),
                ];
            }
            if (PageController::CATEGORY_COMBO === $category->getCategoryType()) {
                $restoMenu[] = [
                    'category' => $category->getName(),
                    'category_type' => $category->getCategoryType(),
                    'description' => $category->getDescription(),
                    'class' => 'menu_promo_title',
                    'price' => $category->getPrice(),
                    'currency' => $category->getCurrency()->getSymbol(),
                    'items' => $this->dishService->getDishesByComboId($category->getId()),
                ];
            }
            if (PageController::CATEGORY_TEXT === $category->getCategoryType()) {
                switch ($category->getName()) {
                    case 'restaurant_name':
                        $restoMenu[] = [
                            'category' => $category->getName(),
                            'category_type' => $category->getCategoryType(),
                            'class' => self::classMapping[$category->getName()],
                            'label' => $this->restaurantRepository->findOneBy(['id' => $restaurantId])->getName(),
                        ];
                        break;
                    default:
                        break;
                }
            }
            if (PageController::CATEGORY_IMAGE === $category->getCategoryType()) {
                $restoMenu[] = [
                    'category' => $category->getName(),
                    'category_type' => $category->getCategoryType(),
                    'class' => self::classMapping[$category->getName()],
                    'link' => $category->getImageUrl(),
                ];
            }
        }

        return $restoMenu;
    }
}
