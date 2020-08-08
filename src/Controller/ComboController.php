<?php

namespace App\Controller;

use App\Entity\Combo;
use App\Entity\ComboDish;
use App\Entity\Currency;
use App\Entity\Dish;
use App\Form\Type\ComboType;
use App\Service\FileUploader;
use App\Service\PagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ComboController extends AbstractController
{
    public function modalShowAddCombo()
    {
        $combo = new Combo();

        $form = $this->createForm(ComboType::class, $combo, ['csrf_protection' => false]);

        return $this->render('user/modals/modal_new_element.twig', [
            'form' => $form->createView(),
            'submitUrl' => $this->generateUrl('submit_add_combo'),
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function removeCombo($id): Response
    {
        /** @var ComboDish $comboDish */
        $comboDish = $this->getDoctrine()->getRepository(ComboDish::class)->findOneBy(['comboId' => $id]);

        if (null !== $comboDish) {
            return new Response(json_encode([
                'status' => 'nok',
                'message' => 'Hay elementos en el combo, no se puede eliminar',
            ]));
        }

        /** @var Combo $combo */
        $combo = $this->getDoctrine()->getRepository(Combo::class)->findOneBy(['id' => $id]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($combo);
        $entityManager->flush();

        return new Response(json_encode([
            'status' => 'ok',
            'message' => 'success',
        ]));
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
        $combo = $this->getDoctrine()->getRepository(Combo::class)->findOneBy(['id' => $comboId]);

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

    public function submitForm(Request $request)
    {
        /** @var Combo $combo */
        $combo = new Combo();

        $form = $this->createForm(ComboType::class, $combo, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $status = 'error';
        $message = '';

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $combo = $form->getData();

                $combo->setEnabled(1);
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
}
