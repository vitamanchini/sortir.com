<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CsvImportForm;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\CsvParserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route("/admin", name:"admin_")]
class AdminController extends AbstractController
{
    #[Route('/', name: 'page')]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'create-user')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('admin_page');
        }

        return $this->render('admin/create-user.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $participant = $entityManager->getRepository(Participant::class)->find($id);
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('page');
        }

        return $this->renderForm('admin/edit.html.twig', [
            'participant' => $participant,
            'form' => $form, //todo create form/view to edit
        ]);
    }

    #[Route('/{id}', name: 'delete')]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $participant = $entityManager->getRepository(Participant::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_page');
    }
    #[Route('/new_users', name: 'create-users-csv')]
    public function newUsersCsv(Request $request,
                                EntityManagerInterface $entityManager,
                                SluggerInterface $slugger,
                                CsvParserService  $parserService): Response
    {

        $form = $this->createForm(CsvImportForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );

                    $filepath = $this->getParameter('uploads_directory') . '/' . $newFilename;
                    $parserService->parseCsv($filepath);

                } catch (FileException $e) {
                    $this->addFlash('fail', 'Something went wrong');
                }
                $this->addFlash('success', 'File uploaded successfully');
            }

        }

        return $this->render('admin/create-users-csv.html.twig', [

            'form' => $form->createView(),
        ]);
    }
}
