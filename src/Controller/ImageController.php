<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\Type\ImageType;
use App\Service\FileUploader;
use App\Service\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends AbstractController
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function modalShowAddImage($sectionId)
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'pageId' => $sectionId,
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_image', ['sectionId' => $sectionId]),
        ]);
    }

    /**
     * @param int          $imageId
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function editImage(int $imageId, Request $request, FileUploader $fileUploader): Response
    {
        /** @var Image $image */
        $image = $this->getDoctrine()->getRepository(Image::class)->findOneBy(['id' => $imageId]);

        $originalImageUri = $image->getImageUrl();

        $form = $this->createForm(ImageType::class, $image, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData();

            $path = 'img/';

            if (17 === $image->getSectionId()) {
                $path .= 'slideshow/';
            }

            if (15 === $image->getSectionId()) {
                $path .= 'equip/';
            }

            $uploadedFile = $form['imageUrl']->getData();

            if ($uploadedFile) {
                $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '');
                $image->setImageUrl($path.$uploadedFilename);
            } else {
                $image->setImageUrl($originalImageUri);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
        }

        return $this->render('pages/edit_image.html.twig', [
            'label' => 'Editar imagen',
            'p' => 0,
            'form' => $form->createView(),
            'target_directory' => $this->targetDirectory,
            'submitUrl' => $this->generateUrl('show_modal_edit_image', ['imageId' => $imageId]),
        ]);
    }

    public function submitForm($sectionId, Request $request, PagesService $pagesService, FileUploader $fileUploader)
    {
        $image = new Image();

        $path = 'img/';

        $form = $this->createForm(ImageType::class, $image, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $image = $form->getData();

                if (17 === (int) $sectionId) {
                    $path .= 'slideshow/';
                }

                if (15 === (int) $sectionId) {
                    $path .= 'equip/';
                }

                $uploadedFile = $form['filename']->getData();
                if ($uploadedFile) {
                    $uploadedFilename = $fileUploader->upload($uploadedFile, $path ?? '');
//                    $uploadedFilename = $fileUploader->upload($uploadedFile, '../../cebio.com.ar/public/'.$path);
                    $image->setFilename($path.$uploadedFilename);
                }

                $image->setSectionId($sectionId);
                $image->setEnabled(1);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($image);
                $entityManager->flush();

                $previousValue = $pagesService->getPageInfoBySectionId($sectionId);

                if (null === $previousValue[0]['content']) {
                    return $this->json([
                        'status' => 'success',
                        'message' => 'guardado'
                    ]);
                }

                $newValue = ['image' => $path.'/'.$image->getFilename()];
                $updateValue = json_encode(array_merge($previousValue[0]['content'], $newValue));
                if (!$pagesService->updatePage($sectionId, $updateValue)) {
                    return $this->json([
                        'status' => 'error',
                        'message' => 'No se pudo actualizar la DB '.var_export($newValue,true),
                    ]);
                };

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
