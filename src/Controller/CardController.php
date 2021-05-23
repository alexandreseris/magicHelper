<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ) {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/card/{card_id}", name="card")
     */
    public function index(string $card_id): Response
    {
        /** @var \App\Entity\Card $card */
        $card = $this->entityManager->getRepository(\App\Entity\Card::class)->findOneBy(["id_scryfall" => $card_id]);
        if (is_null($card)) {
            throw new NotFoundHttpException("card not found, try again :/");
        }
        return $this->render('card/index.html.twig', [
            "card" => $card
        ]);
    }
}
