<?php

declare(strict_types=1);

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


     #[Route("/", name:"main_home")]

    public function home(): Response
    {
        return $this->render('accueil/home.html.twig');
    }

}
