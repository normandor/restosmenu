<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComboDish;
use App\Entity\Currency;
use App\Entity\Dish;
use App\Form\Type\CategoryType;
use App\Form\Type\ComboType;
use App\Service\FileUploader;
use App\Service\PagesService;
use http\Exception\RuntimeException;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function modalShowAddCategory()
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_category'),
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function remove($id): Response
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['categoryId' => $id]);

        if (null !== $dish) {
            return new Response(json_encode([
                'status' => 'nok',
                'message' => 'Hay elementos en la categoria, no se puede eliminar',
            ]));
        }

        $combosWithDishesSelected = $this->getDoctrine()->getRepository(ComboDish::class)->findBy(
            ['comboId' => $id]
        );
        if (count($combosWithDishesSelected) !== 0) {
            return new Response(json_encode([
                'status' => 'nok',
                'message' => 'Este combo contiene platos, no puede ser eliminado',
            ]));
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(
            ['id' => $id, 'categoryType' => PageController::CATEGORY_BASICO]
        );
        if (count($categories) === 1) {
            return new Response(json_encode([
                'status' => 'nok',
                'message' => 'Tiene que haber al menos una categoría',
            ]));
        }

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $id]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return new Response(json_encode([
            'status' => 'ok',
            'message' => 'success',
        ]));
    }

    /**
     * @param int          $categoryId
     * @param Request      $request
     *
     * @return Response
     */
    public function editCategory(int $categoryId, Request $request): Response
    {
        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $categoryId]);

        $form = $this->createForm(CategoryType::class, $category, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }

        return $this->render('pages/edit_item.html.twig', [
            'label' => 'Editar categoria',
            'p' => 0,
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_category', ['categoryId' => $categoryId]),
        ]);
    }

    public function submitForm(Request $request)
    {
        /** @var Category $category */
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $category = $form->getData();

                $lastComboPosition = $this->getDoctrine()->getRepository(Category::class)
                    ->getLastPosition();

                $category->setOrderShow((int) $lastComboPosition['max'] + 1);

                $category->setEnabled(1);
                $category->setCategoryType(PageController::CATEGORY_BASICO);
                $category->setRestaurantId($this->getUser()->getRestaurantId());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();

                $status = 'success';
                $message = 'guardado';
            } else {
                foreach ($form->getErrors(true, false) as $error) {
                    $message .= $error->current()->getMessage();
                }
            }
        }

        return $this->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function modalShowAddCombo()
    {
        $combo = new Category();

        $form = $this->createForm(ComboType::class, $combo, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_combo'),
        ]);
    }

    /**
     * return json
     */
    public function getComboCount()
    {
        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findBy(
            [
                'restaurantId' => $this->getUser()->getRestaurantId(),
                'categoryType' => PageController::CATEGORY_COMBO,
            ]
        );

        return $this->json(['count' => (!$category) ? 0 : count($category)]);
    }

    /**
     * @param int          $comboId
     * @param Request      $request
     *
     * @return Response
     */
    public function editCombo(int $comboId, Request $request): Response
    {
        /** @var Combo $combo */
        $combo = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $comboId]);

        $form = $this->createForm(ComboType::class, $combo, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $combo = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($combo);
            $entityManager->flush();
        }

        return $this->render('pages/edit_item.html.twig', [
            'label' => 'Editar categoria',
            'p' => 0,
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_combo', ['comboId' => $comboId]),
        ]);
    }

    public function submitFormAddCombo(Request $request)
    {
        /** @var Category $combo */
        $combo = new Category();

        $form = $this->createForm(ComboType::class, $combo, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $combo = $form->getData();

                $lastComboPosition = $this->getDoctrine()->getRepository(Category::class)
                    ->getLastPosition();

                $combo->setOrderShow((int) $lastComboPosition['max'] + 1);
                $combo->setEnabled(1);
                $combo->setCategoryType(PageController::CATEGORY_COMBO);
                $combo->setRestaurantId($this->getUser()->getRestaurantId());

                /** @var Currency $currency */
                $currency = $this->getDoctrine()->getRepository(Currency::class)->findOneBy(['id' => 1]);
                $combo->setCurrency($currency);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($combo);
                $entityManager->flush();

                $status = 'success';
                $message = 'guardado';
            } else {
                foreach ($form->getErrors(true, false) as $error) {
                    $message .= $error->current()->getMessage();
                }
            }
        }

        return $this->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * @param int $categoryId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function toggleVisibility(int $categoryId)
    {
        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $categoryId]);

        if (!$category) {
            return $this->json([
                'status'  => 'error',
                'message' => 'Categoria no existente'
            ], 404);
        }

        $category->setEnabled((int)!$category->getEnabled());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'actualizado'
        ], 204);
    }
}
