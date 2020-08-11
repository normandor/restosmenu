<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComboDish;
use App\Entity\Currency;
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

    public function submitFormAddDish(Request $request)
    {
        /** @var Dish $dish */
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
                $dish->setRestaurantId($this->getUser()->getRestaurantId());

                /** @var Currency $currency */
                $currency = $this->getDoctrine()->getRepository(Currency::class)->findOneBy(['id' => 1]);
                $dish->setCurrency($currency);

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

    /**
     * @param int $dishId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function toggleVisibility(int $dishId)
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);

        if (!$dish) {
            return $this->json([
                'status'  => 'error',
                'message' => 'Plato no existente'
            ], 404);
        }

        $dish->setEnabled((int)!$dish->getEnabled());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($dish);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'actualizado'
        ], 204);
    }
}
