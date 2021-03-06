<?php

namespace App\Controller\Manager;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Service\Uploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CardController extends AbstractController
{
    private $cards;

    private $em;

    private $uploader;

    public function __construct(CardRepository $cardRepository, Uploader $uploader, ManagerRegistry $em)
    {
        $this->cards = $cardRepository;
        $this->em = $em;
        $this->uploader = $uploader;
    }

    /**
     * @Route("/manager/cards/new", name="manager_cards_new")
     */
    public function new(Request $request) : Response
    {
        $form = $this->createForm(CardType::class, new Card());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->getManager()->persist($form->getData());
            $this->em->getManager()->flush();

            $this->addFlash(
                'success',
                'message.card.created'
            );

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manager/cards", name="manager_cards_list")
     */
    public function list(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $cards = $this->cards->findBy([], ['active' => 'DESC', 'createdAt' => 'DESC']);

        return $this->render('_manage/card/list.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/edit", name="manager_cards_edit")
     */
    public function edit(Card $card, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cardCover = $form->get('image')->getData();

            if ($cardCover instanceof UploadedFile) {
                $originalFilename = pathinfo($cardCover->getClientOriginalName(), PATHINFO_FILENAME);
                //$newFilename = $originalFilename . '-' . uniqid() . '.' . $cardCover->guessExtension();

                try {
                    $url = $this->uploader->upload($cardCover->getRealPath());

                } catch (FileException $e) {
                    throw new \RuntimeException($e->getMessage());
                }

                $card->setCoverFilename($url);
            }

            $this->em->getManager()->flush();

            $this->addFlash(
                'success',
                'message.card.updated'
            );

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/edit.html.twig', [
            'form' => $form->createView(),
            'card' => $card
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/show", name="manager_cards_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('_manage/card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/remove", name="manager_cards_remove")
     */
    public function remove(Card $card, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->em->getManager()->remove($card);
            $this->em->getManager()->flush();

            $this->addFlash('success', 'Card removed');

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/remove.html.twig', [
            'card' => $card,
        ]);
    }


}