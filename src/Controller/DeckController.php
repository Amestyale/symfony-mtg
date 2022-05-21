<?php

namespace App\Controller;

use App\Classe\Auth;
use App\Classe\Color;
use App\Entity\Card;
use App\Entity\CardDeck;
use App\Entity\Deck;
use App\Form\DeckType;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/decks')]
class DeckController extends AbstractController
{

    #[Route('/', name: 'app_deck_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $decks = $entityManager
            ->getRepository(Deck::class)
            ->findAll();

        return $this->render('deck/index.html.twig', [
            'decks' => $decks,
        ]);
    }

    #[Route('/new', name: 'app_deck_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deck = new Deck();

        $form = $this->buildForm($deck);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deck);
            $entityManager->flush();

            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/new.html.twig', [
            'deck' => $deck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_show', methods: ['GET'])]
    public function show(Deck $deck): Response
    {
        return $this->render('deck/show.html.twig', [
            'deck' => $deck,
            'cards' => $deck->getCards()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deck_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deck $deck, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($deck)
        ->add('name', TextType::class, ['label' => 'Deck name'])
        ->add('description', TextType::class, ['label' => 'Deck description'])
        ->add('submit', SubmitType::class)
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/edit.html.twig', [
            'deck' => $deck,
            'form' => $form,
            'update' => true,
            'cards' => $deck->getCards()
        ]);
    }

    #[Route('/{id}/add-card/{card}', name: 'app_deck_add_card', methods: ['GET'])]
    public function addCard(Deck $deck, Card $card, EntityManagerInterface $entityManager): Response
    {
        $sql = 'INSERT INTO card_deck (card_id, deck_id, quantity)
        VALUES ('.$card->getId().', '.$deck->getId().', 1)
        ON DUPLICATE KEY UPDATE quantity = quantity + 1';
        
        $statement = $entityManager->getConnection()->prepare($sql);
        $statement->execute();

        return $this->redirectToRoute('app_deck_edit', ["id" => $deck->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/remove-card/{card}/{action}', name: 'app_deck_remove_card', methods: ['GET'])]
    public function removeCard(Deck $deck, Card $card, $action, EntityManagerInterface $entityManager): Response
    {
        $cardDeck = $entityManager->find(CardDeck::class, array("card" => $card, "deck" => $deck));
        
        if($action == "all" || $cardDeck->getQuantity() <= 1){
            $entityManager->remove($cardDeck);
        } else {
            $cardDeck->removeOne();
        }
        $entityManager->flush();
        
        return $this->redirectToRoute('app_deck_edit', ["id" => $deck->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}', name: 'app_deck_delete', methods: ['POST'])]
    public function delete(Request $request, Deck $deck, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deck->getId(), $request->request->get('_token'))) {
            $entityManager->remove($deck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
    }

    public function buildForm(Deck $deck){
        $form = $this->createFormBuilder($deck);
        $form->add('name', TextType::class, ['label' => 'Name'])
        ->add('description', TextareaType::class, ['label' => 'Description','required' => false])
        ->add('submit', SubmitType::class);

        return $form->getForm();
    }
}
