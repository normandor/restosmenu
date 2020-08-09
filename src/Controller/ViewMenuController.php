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
        CategoryService $categoryService,
        ComboService $comboService,
        DishService $dishService,
        $page
    ) {
        $restoMenu = [];
        $categories = $categoryService->getEnabledCategoriesByRestaurantId($this->getUser()->getRestaurantId());

        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)
            ->findOneBy(['selected' => 1, 'id' => $this->getUser()->getRestaurantId()]);

        /** @var Category $category */
        foreach ($categories as $category) {
            $restoMenu[] = [
                'categoria' => $category->getName(),
                'items' => $dishService->getDishesByCategoryId($category->getId()),
            ];
        }

        $combos = $comboService->getEnabledCombosByRestaurantId($restaurant->getId());

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

        return $this->render('view_menu_'.$page.'.html.twig', [
            'route' => $request->get('_route'),
            'titulo' => ((null !== $restaurant) ? $restaurant->getName() : ''),
            'logo' => $restaurant->getLogoUrl(),
            'restoMenu' => $restoMenu,
        ]);
    }

    public function showMenu(
        Request $request,
        CategoryService $categoryService,
        ComboService $comboService,
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
        $categories = $categoryService->getEnabledCategoriesByRestaurantId($restaurant->getId());

        /** @var Category $category */
        foreach ($categories as $category) {
            $restoMenu[] = [
                'categoria' => $category->getName(),
                'items' => $dishService->getDishesByCategoryId($category->getId()),
            ];
        }

        $combos = $comboService->getEnabledCombosByRestaurantId($restaurant->getId());

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
