<?php

declare(strict_types=1);

namespace App\Controller;
use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\SearchData;
use App\Form\PlaceFormType;
use App\Form\SearchFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Throwable;

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
    #[Route("/new/place", name: "new_place")]
    public function createPlace(Request $request,
    EntityManagerInterface $entityManager){
        $place = new Place();
        $formTwoEntities = $this->createForm(PlaceFormType::class, $place);
        $formTwoEntities->handleRequest($request);

        if($formTwoEntities->isSubmitted() && $formTwoEntities->isValid()){
            $entityManager->persist($place);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        }
        return $this->render('place/create-place.html.twig', [
            'participant' => $place,
            'form' => $formTwoEntities->createView(),
        ]);


    }


//
//    #[Route("/new/place", name: "new_place")]
//    public function createPlace(Request $request,
//                                EntityManagerInterface $entityManager,
//                                ManagerRegistry $doctrine){
//        $place = new Place();
//        $city = new City();
//        $formPlace = $this->createForm(PlaceFormType::class, $place);
//        $formCity = $this->createForm(CityFormType::class, $city);
//        $formCity->handleRequest($request);
//
//        $formPlace->handleRequest($request);
//
//
//        if($formCity->isSubmitted() && $formCity->isValid()){
//            $entityManager->persist($formCity);
//            $entityManager->flush();
//            return $this->redirectToRoute('main_home');
//        }
//        return $this->render('place/create-place.html.twig', [
//            'participant' => $place,
//            'form' => $formTwoEntities->createView(),
//        ]);
//
//
//    }
}
