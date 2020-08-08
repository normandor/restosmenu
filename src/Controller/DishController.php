<?php

namespace App\Controller;

use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Form\Type\ComboDishType;
use App\Form\Type\ComboSelectedDishType;
use App\Form\Type\DishType;
use App\Service\FileUploader;
use App\Service\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DishController extends AbstractController
{
    public function modalShowAddDish()
    {
        $dish = new Dish();

        $form = $this->createForm(DishType::class, $dish, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_dish'),
        ]);
    }

    /**
     * @param int $comboId
     *
     * @return Response
     */
    public function modalShowAddDishToCombo(int $comboId)
    {
        $comboDish = new ComboDish();

        $form = $this->createForm(ComboDishType::class, $comboDish, ['csrf_protection' => false, 'comboId' => $comboId]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_dish_to_combo'),
        ]);
    }

    /**
     * @param int $dishId
     *
     * @return Response|bool
     */
    public function modalShowAddSelectedDishToCombo(int $dishId)
    {
        $comboDish = new ComboDish();

        $form = $this->createForm(ComboSelectedDishType::class, $comboDish, ['csrf_protection' => false, 'dishId' => $dishId]);

        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);
        if (!$dishId) {
            return false;
        }

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'subtitle' => 'Agregar "'.$dish->getName().'"" a:',
            'submitUrl' => $this->generateUrl('submit_add_selected_dish_to_combo'),
        ]);
    }

    public function submitFormSelectedDishToCombo(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $comboDish = new ComboDish();

        $form = $this->createForm(ComboSelectedDishType::class, $comboDish, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $comboDish = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($comboDish);
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
     * @param $id
     *
     * @return Response
     */
    public function remove($id): Response
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $id]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($dish);
        $entityManager->flush();

        return new Response(json_encode([
            'message' => 'success',
        ]));
    }

    /**
     * @param int $comboId
     * @param int $dishId
     *
     * @return Response
     */
    public function removeDishFromCombo($comboId, $dishId): Response
    {
        /** @var ComboDish $comboDish */
        $comboDish = $this->getDoctrine()->getRepository(ComboDish::class)
            ->findOneBy(['comboId' => $comboId, 'dishId' => $dishId]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comboDish);
        $entityManager->flush();

        return new Response(json_encode([
            'message' => 'success',
        ]));
    }

    /**
     * @param int     $dishId
     * @param Request $request
     *
     * @return Response
     */
    public function editDish(int $dishId, Request $request): Response
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);

        $form = $this->createForm(DishType::class, $dish, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dish = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dish);
            $entityManager->flush();
        }

        return $this->render('pages/edit_item.html.twig', [
            'label' => 'Editar categoria',
            'p' => 0,
            'route' => $request->get('_route'),
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_dish', ['dishId' => $dishId]),
        ]);
    }

    public function submitForm(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $dish = new Dish();

        $form = $this->createForm(DishType::class, $dish, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $dish = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $dish->setEnabled(1);
                $entityManager->persist($dish);
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

    public function submitFormDishToCombo(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $comboDish = new ComboDish();

        $form = $this->createForm(ComboDishType::class, $comboDish, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $comboDish = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($comboDish);
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
}
