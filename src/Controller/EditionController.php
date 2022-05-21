<?php

namespace App\Controller;

use App\Entity\Edition;
use App\Form\EditionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/editions')]
class EditionController extends AbstractController
{
    #[Route('/', name: 'app_edition_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $editions = $entityManager
            ->getRepository(Edition::class)
            ->findBy([],['date' => 'DESC']);

        return $this->render('edition/index.html.twig', [
            'editions' => $editions,
        ]);
    }


    #[Route('/new', name: 'app_edition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $edition = new Edition();
        $form = $this->buildForm($edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($edition);
            $entityManager->flush();

            return $this->redirectToRoute('app_edition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('edition/new.html.twig', [
            'edition' => $edition,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_edition_show', methods: ['GET'])]
    public function show(Edition $edition): Response
    {
        return $this->render('edition/show.html.twig', [
            'edition' => $edition,
            'cards' => $edition->getCards()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_edition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Edition $edition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->buildForm($edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_edition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('edition/edit.html.twig', [
            'edition' => $edition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_edition_delete', methods: ['POST'])]
    public function delete(Request $request, Edition $edition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$edition->getId(), $request->request->get('_token'))) {
            $entityManager->remove($edition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_edition_index', [], Response::HTTP_SEE_OTHER);
    }

    public function buildForm(Edition $edition = null) : FormInterface
    {
        $form = $this->createFormBuilder($edition);
        if(!$edition->getId()) $form->add('id', TextType::class, ['label' => 'Code']);

        $form->add('name', TextType::class, ['label' => 'Name'])
        ->add('date', DateType::class, ['label' => 'Release date','years' => range('1993', date('Y')+2)])
        ->add('icon', TextType::class, ['label' => 'Icon'])
        ->add('submit', SubmitType::class);

        return $form->getForm();
    }
}
