<?php

namespace App\Controller;

use App\Service\HttpClient;
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
    private HttpClient $httpclient;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        HttpClient $httpclient)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->httpclient = $httpclient;
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
        // retrieve face images if not existing
        /** @var \App\Entity\Face $face */
        foreach ($card->getFaces() as $face) {
            if (! $face->getImageLocal() && $face->getImageUrl()) {
                $queryBuilder = $this->entityManager->createQueryBuilder();
                $queryBuilder->select('f.image_local')
                    ->from('\App\Entity\Face', 'f')
                    ->where($queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('f.image_url', '?1'),
                        $queryBuilder->expr()->isNotNull('f.image_local'),
                    ))
                    ->setParameter(1, $face->getImageUrl())
                    ->setFirstResult(0)
                    ->setMaxResults(1);
                $faceSearch = $queryBuilder->getQuery()->getResult();

                if (count($faceSearch) > 0) {
                    $face->setImageLocal($faceSearch[0]["image_local"]);
                    $this->entityManager->persist($face);
                    $this->entityManager->flush();
                } else {
                    $filename = $face->getFaceId() . '.jpg';
                    $publicPath = $this->getParameter("app.faces_images_public") . '/' . $filename;
                    $filePath = $this->getParameter("kernel.project_dir") . '/' . $publicPath;
                    $this->logger->debug(
                        'image not found for face id: ' . $face->getFaceId() . " " .
                        'begin download from ' . $face->getImageUrl() . " " .
                        'saving to ' . $filePath . " " .
                        'public path will be ' . $publicPath
                    );
                    $this->httpclient->downloadFile($face->getImageUrl(), $filePath);
                    $face->setImageLocal($filename);
                    $this->entityManager->persist($face);
                    $this->entityManager->flush();
                }
            }
        }
        return $this->render('card/index.html.twig', [
            "card" => $card
        ]);
    }
}
