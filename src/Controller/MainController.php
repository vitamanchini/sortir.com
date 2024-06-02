<?php

declare(strict_types=1);

namespace App\Controller;
use App\Entity\Participant;
use App\Entity\SearchData;
use App\Form\SearchFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MainController extends AbstractController
{


    #[Route("/", name: "main_home")]
    public function home(#[CurrentUser] participant $user,
                         SortieRepository           $sortieRepository,
                         Request                    $request,
                         EntityManagerInterface     $entityManager
    ): Response
    {
        $sorties = $sortieRepository->findAll();

        $searchData = new SearchData();
        $searchData->setUserId($user->getId());
        $filters = $this->createForm(SearchFormType::class, $searchData);

        $filters->handleRequest($request);
        if ($filters->isSubmitted()) {
            $sorties = $sortieRepository->findSearch($searchData);
        }

        return $this->render('accueil/home.html.twig', [
            'user' => $user,
            'sorties' => $sorties,
            'filters' => $filters->createView()
        ]);
    }
//    #[Route("/filter", name:"main_filter")]
//    public function filter(#[CurrentUser] participant $user,
//                           SortieRepository $sortieRepository,
//                           Request $request,
//                           EntityManagerInterface $entityManager): Response
//    {
//        $searchData = new SearchData();
//
//        $filters = $this->createForm(SearchFormType::class, $searchData);
//
//        $filters->handleRequest($request);
//        $sorties = $sortieRepository->findSearch($searchData);
//        if($filters->isSubmitted()){
//            $entityManager->persist($searchData);
//            $entityManager->flush();
//        }
//        return $this->render('accueil/home.html.twig', [
//            'user' => $user,
//            'sorties' => $sorties,
//            'filters' => $filters->createView()
//        ]);
//    }
    #[Route("/demo", name: "demo")]
    public function demo(EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $user->setEmail("demo@mail.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword("404040")
            ->setActive(true)
            ->setName("Jean-Luc")
            ->setSecondName("Picard")
            ->setPseudo("Picard")
            ->setTelephone("+13 666 666");

        dump($user);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('accueil/home.html.twig');
    }

    public function checkMeInscribed($idSortie, $idParticipant, SortieRepository $sortieRepository)
    {

    }
}
