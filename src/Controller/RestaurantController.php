<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\Type\RestaurantType;
use App\Service\FileUploader;
use App\Service\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestaurantController extends AbstractController
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function modalShowAdd()
    {
        ///////////////////////////////////////
        // temporary until multiple restaurants
        $restaurant = new Restaurant();

        $form = $this->createForm(RestaurantType::class, $restaurant, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_restaurant'),
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function remove($id): Response
    {
        ///////////////////////////////////////
        // temporary until multiple restaurants
        return new Response(json_encode([
            'message' => 'invalid action',
        ]));
        ///////////////////////////////////////

        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findOneBy(['id' => $id]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($restaurant);
        $entityManager->flush();

        return new Response(json_encode([
            'message' => 'success',
        ]));
    }

    /**
     * @param int          $restaurantId
     * @param Request      $request
     *
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function edit(int $restaurantId, Request $request, FileUploader $fileUploader): Response
    {
        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);

        $originalImageUri = $restaurant->getLogoUrl();

        $form = $this->createForm(RestaurantType::class, $restaurant, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant = $form->getData();

            $path = 'images/logos/';
            $uploadedFile = $form['logoUrl']->getData();

            if ($uploadedFile) {
                $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '');
                $restaurant->setLogoUrl($path.$uploadedFilename);
            } else {
                $restaurant->setLogoUrl($originalImageUri);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();
        }

        return $this->render('pages/edit_restaurant.html.twig', [
            'label' => 'Editar restaurant',
            'target_directory' => $this->targetDirectory,
            'p' => 0,
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_restaurant', ['restaurantId' => $restaurantId]),
        ]);
    }

    public function submitForm(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $restaurant = new Restaurant();

        $form = $this->createForm(RestaurantType::class, $restaurant, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $restaurant = $form->getData();

                $restaurant->setEnabled(1);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($restaurant);
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
