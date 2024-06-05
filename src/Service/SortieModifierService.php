<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Exception\CreateNotFoundException;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SortieModifierService
{
    private $entityManager;
    private $formFactory;
    private $router;

    private $twig;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RouterInterface $router, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
    }


    public function edit(int $id, Request $request): Response
    {
        $sortie = $this->entityManager->getRepository(Sortie::class)->find($id);

        $form = $this->formFactory->create(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return new RedirectResponse($this->router->generate('sortie_show', ['id' => $sortie->getId()]));
        } else{
            $request->getSession()->getFlashBag()->add("error", "La sortie n'a pas été mis à jour");
        }

        return $this->render([
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }



    private function render(array $parameters = []): Response
    {
        $content = $this->twig->render('sortie/edit.html.twig', $parameters);

        return new Response($content);
    }

}