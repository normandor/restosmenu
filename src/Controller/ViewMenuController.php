<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Combo;
use App\Entity\Restaurant;
use App\Entity\SettingsPage;
use App\Service\DishService;
use App\Service\SettingsPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ViewMenuController extends AbstractController
{
    public function showPreview(
        Request $request,
        DishService $dishService,
        SettingsPageService $settingsPageService,
        $page
    ) {
        $restoMenu = [];
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_BASICO,
            ]);

        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findOneBy(['selected' => 1, 'id' => $this->getUser()->getRestaurantId()]);

        /** @var Category $category */
        foreach ($categories as $category) {
            $restoMenu[] = [
                'categoria' => $category->getName(),
                'class' => 'menu_category',
                'items' => $dishService->getDishesByCategoryId($category->getId()),
            ];
        }

        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_COMBO,
            ]);

        /** @var Combo $combo */
        foreach ($combos as $combo) {
            $restoMenu[] = [
                'categoria' => $combo->getName(),
                'description' => $combo->getDescription(),
                'class' => 'menu_promo_title',
                'price' => $combo->getPrice(),
                'currency' => $combo->getCurrency()->getSymbol(),
                'items' => $dishService->getDishesByComboId($combo->getId()),
            ];
        }

        $properties = $settingsPageService->getPropertiesByRestaurantId($this->getUser()->getRestaurantId());

        return $this->render('view_menu_'.$page.'.html.twig', [
            'route' => $request->get('_route'),
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $restaurant->getLogoUrl(),
            'properties' => $properties,
            'restoMenu' => $restoMenu,
        ]);
    }

    public function showMenu(
        DishService $dishService,
        $restaurantSlug
    ) {
        if (0 === $restaurantSlug) {
            throw new NotFoundHttpException('Sorry not existing!');
        }

        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findOneBy(['slug' => $restaurantSlug]);

        if (!$restaurant) {
            throw new NotFoundHttpException('Restaurant not found');
        }

        $restoMenu = [];
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_BASICO,
            ]);

        /** @var Category $category */
        foreach ($categories as $category) {
            $restoMenu[] = [
                'categoria' => $category->getName(),
                'items' => $dishService->getDishesByCategoryId($category->getId()),
            ];
        }

        $combos = $this->getDoctrine()->getRepository(Category::class)
            ->findBy([
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_COMBO,
            ]);

        /** @var Combo $combo */
        foreach ($combos as $combo) {
            $restoMenu[] = [
                'categoria' => $combo->getName(),
                'description' => $combo->getDescription(),
                'price' => $combo->getPrice(),
                'currency' => $combo->getCurrency()->getSymbol(),
                'items' => $dishService->getDishesByComboId($combo->getId()),
            ];
        }
        return $this->render('view_menu_1_public.html.twig', [
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $restaurant->getLogoUrl(),
            'restoMenu' => $restoMenu,
        ]);
    }
}
