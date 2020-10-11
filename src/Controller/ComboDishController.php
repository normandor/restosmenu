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


class ComboDishController extends AbstractController
{
    public function submitFormSelectedDishToCombo(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        /** @var ComboDish $comboDish */
        $comboDish = new ComboDish();

        $form = $this->createForm(ComboSelectedDishType::class, $comboDish, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $comboDish = $form->getData();

                $lastComboPosition = $this->getDoctrine()->getRepository(ComboDish::class)
                    ->getLastPosition();
                $comboDish->setOrderShow((int) $lastComboPosition['max'] + 1);
                $comboDish->setRestaurantId($this->getUser()->getRestaurantId());

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

                $lastComboPosition = $this->getDoctrine()->getRepository(ComboDish::class)
                    ->getLastPosition();

                $comboDish->setOrderShow((int) $lastComboPosition['max'] + 1);
                $comboDish->setRestaurantId($this->getUser()->getRestaurantId());

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
     * @param int $comboId
     * @param int $dishId
     *
     * @return Response
     */
    public function removeDishFromCombo($comboId, $dishId): Response
    {
        $comboDishRepository = $this->getDoctrine()->getRepository(ComboDish::class);
        /** @var ComboDish $comboDish */
        $comboDish = $comboDishRepository->findOneBy(['comboId' => $comboId, 'dishId' => $dishId]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comboDish);
        $entityManager->flush();

        $this->getDoctrine()->getManager()->createQuery('
            UPDATE App\Entity\ComboDish u
            SET u.orderShow = u.orderShow - 1
            WHERE u.orderShow > :initial
        ')->setParameter('initial', $comboDish->geOrderShow())
        ->execute();

        return new Response(json_encode([
            'message' => 'success',
        ]));
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
            'subtitle' => 'add_dish_x_to',
            'params' => ['%dish%' => $dish->getName()],
            'submitUrl' => $this->generateUrl('submit_add_selected_dish_to_combo'),
        ]);
    }

}
