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
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


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
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return JsonResponse
     */
    public function submitFormAddDish(Request $request, FileUploader $fileUploader): JsonResponse
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
                $restaurantId = $this->getUser()->getRestaurantId();
                $dish->setRestaurantId($restaurantId);

                /** @var Currency $currency */
                $currency = $this->getDoctrine()->getRepository(Currency::class)->findOneBy(['id' => 1]);
                $dish->setCurrency($currency);

                $entityManager->persist($dish);
                $entityManager->flush();

                $path = 'images/dishes/'.$restaurantId.'/';
                $uploadedFile = $form['imageUrl']->getData();

                if ($uploadedFile) {
                    $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '', md5('avatars_'.$dish->getId()));
                    $dish->setImageUrl($path.$uploadedFilename);
                    $entityManager->persist($dish);
                    $entityManager->flush();
                }

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
     * @param int          $dishId
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function editDish(int $dishId, Request $request, FileUploader $fileUploader): Response
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);

        $originalImageUri = $dish->getImageUrl();

        $form = $this->createForm(DishType::class, $dish, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dish = $form->getData();

            $path = 'images/dishes/'.$this->getUser()->getRestaurantId().'/';
            $uploadedFile = $form['imageUrl']->getData();

            if ($uploadedFile) {
                $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '', md5('avatars_'.$dish->getId()));
                $dish->setImageUrl($path.$uploadedFilename);
            } else {
                $dish->setImageUrl($originalImageUri);
            }

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
     * @param $dishId
     *
     * @return Response
     */
    public function removeImageFromDish($dishId): Response
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);

        if (null === $dish->getImageUrl()) {
            return new Response(json_encode([
                'message' => 'success',
                'detail' => 'Image already empty',
            ]),200);
        }

        if (file_exists($dish->getImageUrl())) {
            unlink($dish->getImageUrl());
        }

        $dish->setImageUrl(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($dish);
        $entityManager->flush();

        return new Response(json_encode([
            'message' => 'success',
            'detail' => 'Image removed',
        ]),200);
    }

    /**
     * @param int                 $dishId
     * @param TranslatorInterface $translator
     *
     * @return JsonResponse
     */
    public function toggleVisibility(int $dishId, TranslatorInterface $translator)
    {
        /** @var Dish $dish */
        $dish = $this->getDoctrine()->getRepository(Dish::class)->findOneBy(['id' => $dishId]);

        if (!$dish) {
            return $this->json([
                'status'  => 'error',
                'message' => $translator->trans('Dish_not_exist'),
            ], 404);
        }

        $dish->setEnabled((int)!$dish->getEnabled());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($dish);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => $translator->trans('updated'),
        ], 204);
    }
}
