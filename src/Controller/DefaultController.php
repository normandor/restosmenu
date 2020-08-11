<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComboDish;
use App\Entity\Dish;
use App\Entity\Restaurant;
use App\Entity\SettingsPage;
use App\Service\PagesService;
use App\Service\SettingsPageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('pages/main_page.html.twig', [
            'title' => 'Bienvenidos',
        ]);
    }
}
