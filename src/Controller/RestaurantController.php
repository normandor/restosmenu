<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Dish;
use App\Entity\Restaurant;
use App\Entity\SettingsImage;
use App\Entity\SettingsPage;
use App\Entity\SettingsPagePreview;
use App\Form\Type\AddRestaurantType;
use App\Form\Type\DishType;
use App\Form\Type\RestaurantType;
use App\Service\FileUploader;
use App\Service\PagesService;
use App\Service\RestaurantService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zxing\NotFoundException;

class RestaurantController extends AbstractController
{
    const LOGO_CATEGORY_TYPE = 'image';
    const LOGO_CATEGORY_NAME = 'restaurant_logo';

    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function showRestaurantList(Request $request)
    {
        return $this->render('user/restaurant_list.html.twig', [
            'user' => '$this->getUser()->getName()',
            'label' => 'restaurant_management',
            'p' => 0,
            'route' => $request->get('_route'),
        ]);
    }

    /**
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
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
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
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
     * @param                   $id
     * @param RestaurantService $restaurantService
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function modalShowEditRestaurant($id, RestaurantService $restaurantService, Request $request): Response
    {
        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findOneBy(['id' => $id]);

        $form = $this->createForm(RestaurantType::class, $restaurant, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();
        }

        return $this->render('pages/edit_item.html.twig', [
            'label' => 'edit_restaurant',
            'route' => $request->get('_route'),
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_restaurant', ['id' => $id]),
        ]);
    }

    /**
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function modalShowAddRestaurant()
    {
        $restaurant = new Restaurant();

        $form = $this->createForm(AddRestaurantType::class, $restaurant, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_restaurant'),
        ]);
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
        if ($this->getUser()->getRestaurantId() !== $restaurantId) {
            throw $this->createNotFoundException();
        }

        /** @var Restaurant $restaurant */
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findOneBy(['id' => $restaurantId]);

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(
            [
                'restaurantId' => $restaurantId,
                'categoryType' => self::LOGO_CATEGORY_TYPE,
                'name' => self::LOGO_CATEGORY_NAME,
            ]
        );

        $originalImageUri = $category->getImageUrl();

        $form = $this->createForm(RestaurantType::class, $restaurant, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant = $form->getData();

            $path = 'images/logos/'.$restaurantId.'/';
            $uploadedFile = $form['logoUrl']->getData();

            if ($uploadedFile) {
                $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '', md5('logos_'.$restaurantId));
                $category->setImageUrl($path.$uploadedFilename);
            } else {
                $category->setImageUrl($originalImageUri);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();
        }

        if (!$form->isSubmitted()) {
            $form->get('logoUrl')->setData($originalImageUri);
        }

        return $this->render('pages/edit_restaurant.html.twig', [
            'label' => 'Editar restaurant',
            'target_directory' => $this->targetDirectory,
            'p' => 0,
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('show_modal_edit_restaurant', ['restaurantId' => $restaurantId]),
        ]);
    }

    /**
     * @param Request      $request
     * @param PagesService $pagesService
     * @param FileUploader $fileUploader
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function submitForm(Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $restaurant = new Restaurant();

        $form = $this->createForm(AddRestaurantType::class, $restaurant, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $restaurant = $form->getData();

                $restaurant->setEnabled(1);
                $restaurant->setSelected(1);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($restaurant);
                $entityManager->flush();

                $this->addNewRestaurantDefaultSettings($restaurant->getId());

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
     * @param int $restaurantId
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    private function addNewRestaurantDefaultSettings(int $restaurantId): void
    {
        $defaultImageValues = [
            ['key' => 'restaurant_logo', 'name' => 'Logo restaurant', 'property' => 'visible', 'value' => 'false', 'value_mobile' => null, 'restaurant_id' => $restaurantId,],
            ['key' => 'restaurant_logo', 'name' => 'Logo restaurant', 'property' => 'width', 'value' => '30%', 'value_mobile' => '30%', 'restaurant_id' => $restaurantId,],
            ['key' => 'dish', 'name' => 'Platos', 'property' => 'visible', 'value' => 'true', 'value_mobile' => null, 'restaurant_id' => $restaurantId,],
            ['key' => 'dish', 'name' => 'Platos', 'property' => 'width', 'value' => '30%', 'value_mobile' => '30%', 'restaurant_id' => $restaurantId,],
        ];

        $defaultPageValues = [
            ['key' => 'menu_restaurant_title', 'name' => 'Titulo restaurant', 'property' => 'font-family', 'value' => '"Arial Black", Gadget, sans-serif', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_restaurant_title', 'name' => 'Titulo restaurant', 'property' => 'font-size', 'value' => '30px', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_restaurant_title', 'name' => 'Titulo restaurant', 'property' => 'color', 'value' => '#b86d05', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_restaurant_title', 'name' => 'Titulo restaurant', 'property' => 'background-color', 'value' => '', 'restaurant_id' => $restaurantId,],

            ['key' => 'menu_body', 'name' => 'Cuerpo de la carta', 'property' => 'font-family', 'value' => '', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_body', 'name' => 'Cuerpo de la carta', 'property' => 'font-size', 'value' => '', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_body', 'name' => 'Cuerpo de la carta', 'property' => 'color', 'value' => '#b86d05', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_body', 'name' => 'Cuerpo de la carta', 'property' => 'background-color', 'value' => '#e4d7af', 'restaurant_id' => $restaurantId,],

            ['key' => 'menu_category', 'name' => 'Categorias', 'property' => 'font-family', 'value' => '"Arial Black", Gadget, sans-serif', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_category', 'name' => 'Categorias', 'property' => 'font-size', 'value' => '26px', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_category', 'name' => 'Categorias', 'property' => 'color', 'value' => '#b86d05', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_category', 'name' => 'Categorias', 'property' => 'background-color', 'value' => '', 'restaurant_id' => $restaurantId,],

            ['key' => 'menu_promo_title', 'name' => 'Promociones', 'property' => 'font-family', 'value' => '"Arial Black", Gadget, sans-serif', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_promo_title', 'name' => 'Promociones', 'property' => 'font-size', 'value' => '26px', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_promo_title', 'name' => 'Promociones', 'property' => 'color', 'value' => '#b86d05', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_promo_title', 'name' => 'Promociones', 'property' => 'background-color', 'value' => '', 'restaurant_id' => $restaurantId,],

            ['key' => 'menu_dish', 'name' => 'Platos', 'property' => 'font-family', 'value' => '"Arial Black", Gadget, sans-serif', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_dish', 'name' => 'Platos', 'property' => 'font-size', 'value' => '22px', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_dish', 'name' => 'Platos', 'property' => 'color', 'value' => '#b86d05', 'restaurant_id' => $restaurantId,],
            ['key' => 'menu_dish', 'name' => 'Platos', 'property' => 'background-color', 'value' => '', 'restaurant_id' => $restaurantId,],
        ];

        $defaultCategoryValues = [
            ['category_type' => 'text', 'name' => 'restaurant_name', 'description' => '', 'enabled' => 1, 'order_show' => 1, 'restaurant_id' => $restaurantId,],
            ['category_type' => 'image', 'name' => 'restaurant_logo', 'description' => '', 'enabled' => 0, 'order_show' => 2, 'restaurant_id' => $restaurantId,],
            ['category_type' => 'basico', 'name' => 'Entradas', 'description' => '', 'enabled' => 1, 'order_show' => 3, 'restaurant_id' => $restaurantId,],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($defaultImageValues as $key => $value) {
            /** @var SettingsImage $settingsImage */
            $settingsImage = new SettingsImage();
            $settingsImage->setKey($value['key']);
            $settingsImage->setName($value['name']);
            $settingsImage->setProperty($value['property']);
            $settingsImage->setValue($value['value']);
            $settingsImage->setValueMobile($value['value_mobile']);
            $settingsImage->setRestaurantId($value['restaurant_id']);

            $entityManager->persist($settingsImage);
        }

        foreach ($defaultPageValues as $key => $value) {
            /** @var SettingsPage $settingsPage */
            $settingsPage = new SettingsPage();
            $settingsPage->setKey($value['key']);
            $settingsPage->setName($value['name']);
            $settingsPage->setProperty($value['property']);
            $settingsPage->setValue($value['value']);
            $settingsPage->setRestaurantId($value['restaurant_id']);

            $entityManager->persist($settingsPage);

            /** @var SettingsPagePreview $settingsPagePreview */
            $settingsPagePreview = new SettingsPagePreview();
            $settingsPagePreview->setKey($value['key']);
            $settingsPagePreview->setName($value['name']);
            $settingsPagePreview->setProperty($value['property']);
            $settingsPagePreview->setValue($value['value']);
            $settingsPagePreview->setRestaurantId($value['restaurant_id']);
            $settingsPagePreview->setIsSynced(1);

            $entityManager->persist($settingsPagePreview);
        }

        foreach ($defaultCategoryValues as $key => $value) {
            /** @var Category $category */
            $category = new Category();
            $category->setCategoryType($value['category_type']);
            $category->setName($value['name']);
            $category->setDescription($value['description']);
            $category->setEnabled($value['enabled']);
            $category->setOrderShow($value['order_show']);
            $category->setRestaurantId($value['restaurant_id']);

            $entityManager->persist($category);
        }
        $entityManager->flush();
    }

    /**
     * @param $restaurantId
     *
     * @return Response
     */
    public function removeLogo(int $restaurantId): Response
    {
        if ($this->getUser()->getRestaurantId() !== $restaurantId) {
            throw $this->createNotFoundException();
        }

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(
            [
                'restaurantId' => $restaurantId,
                'categoryType' => self::LOGO_CATEGORY_TYPE,
                'name' => self::LOGO_CATEGORY_NAME,
            ]
        );

        if (null === $category->getImageUrl()) {
            return new Response(json_encode([
                'message' => 'success',
                'detail' => 'Image already empty',
            ]),200);
        }

        if (file_exists($category->getImageUrl())) {
            unlink($category->getImageUrl());
        }

        $category->setImageUrl(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);
        $entityManager->flush();

        return new Response(json_encode([
            'message' => 'success',
            'detail' => 'Image removed',
        ]),200);
    }

    /**
     * @param string            $dbuser
     * @param string            $dbpw
     * @param string            $dbname
     * @param string            $dbhost
     * @param Request           $request
     * @param RestaurantService $restaurantService
     *
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Not found")
     * @IsGranted("ROLE_MANAGER", statusCode=404, message="Not found")
     */
    public function getTableData($dbuser, $dbpw, $dbname, $dbhost, Request $request, RestaurantService $restaurantService)
    {
        $resp = new Response(json_encode($restaurantService->getTableData($dbuser, $dbpw, $dbname, $dbhost, $request->query->all())));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }
}
