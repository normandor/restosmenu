<?php

namespace App\Controller;

use App\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends AbstractController
{

    public function index(Request $request, $route, $childrenId = 0)
    {
        $menuRepository = $this->getDoctrine()->getRepository(Menu::class);

        $parents = $menuRepository->findParentByChildrenId($childrenId);

        if (is_array($parents))
        {
            $parents[] = $childrenId;
        }

        $menu = $menuRepository->findChildrenByParentId(0, $this->getUser()->getRoles());

        return $this->render('left_panel.html.twig', [
                    'route' => $route,
                    'menu' => $menu,
                    'number' => $request->query->get('number'),
                    'selectedId' => $parents
        ]);
    }

}
