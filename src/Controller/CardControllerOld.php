<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Edition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cardsold')]
class CardControllerOld extends AbstractController
{
    #[Route('/{page}', name: 'app_card', requirements: ['page' => '\d+'])]
    public function index(int $page, EntityManagerInterface $em): Response
    {
		$pageSize = 10;
		$firstResult = ($page - 1) * $pageSize;

        $repository = $em->getRepository(Card::class);
        $cards = $repository->findBy(array(),array(),$pageSize,$firstResult);
        

        return $this->render('card/index.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/add', name: 'app_card_add')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $card = new Card();
        
        $form = $this->createFormBuilder($card)
        ->add('name', TextType::class, ['label' => 'Card name'])
        ->add('cost', TextType::class, ['label' => 'Card cost'])
        ->add('description', TextType::class, ['label' => 'Card description'])
        ->add('edition', EntityType::class, [
            'class' => Edition::class,
            'choice_label' => 'name',
        ])
        ->add('submit', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $card = $form->getData();
            $em->persist($card);
            $em->flush();

            return $this->redirectToRoute('app_card');
        } 

        return $this->render('card/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
