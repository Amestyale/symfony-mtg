<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Edition;
use App\Form\CardType;
use App\Form\EditionType;
use App\Repository\CardRepository;
use App\Repository\EditionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cards')]
class CardController extends AbstractController
{
    #[Route('/new', name: 'app_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('app_card_show', ['id' => $card->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, EntityManagerInterface $entityManager): Response
    {
        $form = $this->buildForm($card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_card_show', ["id" => $card->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/edit.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, EntityManagerInterface $entityManager): Response
    {
        $edition = $card->getEdition();

        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_edition_index', ['id' => $edition->getId()], Response::HTTP_SEE_OTHER);
    }

    
    public function buildForm($card) : FormInterface
    {
        $form = $this->createFormBuilder($card)
        ->add('name', TextType::class, ['label' => 'Name'])
        ->add('cost', TextType::class, ['label' => 'Cost'])
        ->add('description', TextareaType ::class, ['label' => 'Content'])
        ->add('image', TextType::class, ['label' => 'Image'])
        ->add('rarity', ChoiceType::class, ['label' => 'Rarity',
            'choices'  => [
                'Mythic' => 'mythic',
                'Rare' => 'rare',
                'Uncommon' => 'uncommon',
                'Common' => 'common',
            ],
        ])
        ->add('edition', EntityType::class, [
            'label' => 'Edition',
            'class' => Edition::class,
            'choice_label' => 'name',
            'query_builder'=>function(EditionRepository $er){
                return $er ->createQueryBuilder('e')->orderBy('e.date','ASC');
            },
            'group_by' => function($edition) {
                return $edition->getDate()->format('Y');
            }
        ])
        ->add('submit', SubmitType::class)
        ->getForm();

        return $form;
    }
}
