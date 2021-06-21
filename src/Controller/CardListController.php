<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardListController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @Route("/cards/", name="cards", methods={"POST", "GET"})
     */
    public function index(Request $request, SessionInterface $session): Response {
        // need to pass this as opts in configuration
        $defaultLimit = 20;
        $maxLimit = 100;
        $limitsAvailable = [20, 50, 100];
        $defaultPostFields = [
            "cardname" => "",
            "setcode" => [],
            "rarity" => [],
            "color" => []
        ];

        // if the user specified a limit and != from session, save the limit in session, else take the session or default
        $limit = $request->query->getInt("limit", 0);
        $sessionLimit = intval($session->get("limit", 0));
        if ($limit === 0) {
            if ($sessionLimit === 0) {
                $limit = $defaultLimit;
                $session->set("limit", $defaultLimit);
            } else {
                $limit = $sessionLimit;
            }
        } else {
            $limit = abs($limit);
            $limit = min([$maxLimit, $limit]);
            $limit = max([1, $limit]);
            if ($limit !== $sessionLimit) {
                $session->set("limit", $limit);
            }
        }

        $pageNumber = abs($request->query->getInt("pageNumber", 1));
        if ($pageNumber === 0) {
            $pageNumber = 1;
        }

        // $orderby = $request->query->get("orderby", "card.id_scryfall"); // completly insecure :O need some restriction !!!!
        // $order = $request->query->get("order", "ASC");
        // if (! in_array($order, ["ASC", "DESC"])) {
        //     $order = "ASC";
        // }
        $orderby = "card.id_scryfall";
        $order = "ASC";

        $headers = ["Card's name", "Set", "Rarity", "Color identity"];
        $rarities = $this->entityManager->createQueryBuilder()
            ->from(\App\Entity\Rarity::class, "rarity")
            ->addOrderBy("rarity.index_value")
            ->addOrderBy("rarity.name")
            ->select(["rarity.name", "rarity.color"])
            ->getQuery()
            ->getResult()
        ;
        $sets = $this->entityManager->createQueryBuilder()
            ->from(\App\Entity\Set::class, "setofcard")
            ->join("setofcard.type", "settype")
            ->addOrderBy("settype.name")
            ->addOrderBy("setofcard.released_date", "DESC")
            ->addOrderBy("setofcard.name")
            ->select(["setofcard.code as setCode", "setofcard.name as setName", "setofcard.icon_local", "settype.code as setTypeCode", "settype.name as setTypeName", "setofcard.released_date"])
            ->getQuery()
            ->getResult()
        ;
        $setTree = [];
        /** @var \App\Entity\Set $set */
        foreach($sets as $set) {
            $set["year"] = $set["released_date"]->format("Y");
            $setType = $set["setTypeName"];
            if (! array_key_exists($setType, $setTree)) {
                $setTree[$setType] = [];
            }
            $setYear = $set["year"];
            if (! array_key_exists($setYear, $setTree[$setType])) {
                $setTree[$setType][$setYear] = [];
            }
            $setTree[$setType][$setYear][] = $set;
        }

        $colors = $this->entityManager->createQueryBuilder()
            ->from(\App\Entity\Color::class, "color")
            ->join("color.symbols", "symbols")
            ->addOrderBy("symbols.index_value")
            ->select(["color.code", "color.name", "symbols.icon_local"])
            ->getQuery()
            ->getResult()
        ;
        // adding a pseudo null value to match card without color identity
        $colors[] = "";

        if ($request->isMethod("GET")) {
            $searchFilters = $session->get("searchFilters", $defaultPostFields);
        } else {
            $searchFilters = $request->request->all();
            $searchFilters = array_merge($defaultPostFields, $searchFilters);
            if (
                gettype($searchFilters['cardname']) !== "string" ||
                gettype($searchFilters['setcode']) !== "array" ||
                gettype($searchFilters['rarity']) !== "array" ||
                gettype($searchFilters['color']) !== "array"
                ) {
                throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("parameters of wrong types!");
            }
            $session->set("searchFilters", $searchFilters);
        }

        $qb = $this->entityManager->createQueryBuilder();

        $whereBuild = $qb->expr()->andX();
        $whereParams = [];

        $qb
            ->from(\App\Entity\Card::class, "card")
            ->join("card.set", "setofcard")
            ->join("card.rarity", "rarity")
            ->join("card.faces", "faces")
        ;


        $cardnameFilter = $searchFilters['cardname'];
        if ($cardnameFilter !== '') {
            $cardnameFilter = '%' . strtolower($cardnameFilter) . '%';
            // little trick: the page return card, but name is on the face side
            // to avoid some face to be excluded,  a subquery is used
            $subQuery = $this->entityManager->createQueryBuilder()
                ->from(\App\Entity\Face::class, "subFaces")
                ->andWhere("subFaces.card = card.id_scryfall")
                ->andWhere("LOWER(subFaces.name) LIKE :cardname")
                ->select(["subFaces"])
                ->getDQL()
            ;
            $whereParams['cardname'] = $cardnameFilter;
            $whereBuild->add($qb->expr()->exists($subQuery));
        }

        $setsFilter = $searchFilters['setcode'];
        if (count($setsFilter) > 0) {
            $whereBuild->add($qb->expr()->in('setofcard.code', ':setcode'));
            $whereParams['setcode'] = $setsFilter;
        }

        $rarirtyFilter = $searchFilters['rarity'];
        if (count($rarirtyFilter) > 0) {
            $whereBuild->add($qb->expr()->in('rarity.name', ':rarity'));
            $whereParams['rarity'] = $rarirtyFilter;
        }

        $colorFilter = $searchFilters['color'];
        if (count($colorFilter) > 0) {
            if (in_array("", $colorFilter)) {
                // dealing with cards having no color
                $qb
                    ->leftJoin("card.color_identity", "color_identity")
                    ->leftJoin("color_identity.symbols", "symbols");
                $values = array_filter($colorFilter, function($item) {
                    return $item !== "";
                });
                if (count($values) > 0) {
                    $whereBuild->add(
                        $qb->expr()->orX(
                            $qb->expr()->in('color_identity.code', ':color'),
                            $qb->expr()->isNull('color_identity.code')
                        )
                    );
                    $whereParams['color'] = $colorFilter;
                } else {
                    $whereBuild->add($qb->expr()->isNull('color_identity.code'));
                }
            } else {
                $qb
                    ->join("card.color_identity", "color_identity")
                    ->join("color_identity.symbols", "symbols")
                ;
                $whereBuild->add($qb->expr()->in('color_identity.code', ':color'));
                $whereParams['color'] = $colorFilter;
            }
        } else {
            $qb
            ->join("card.color_identity", "color_identity")
            ->join("color_identity.symbols", "symbols")
        ;
        }

        $qb
            ->select(["card", "setofcard", "rarity", "color_identity", "symbols", "faces"])
            ->addorderBy($orderby, $order)
            ->setMaxResults($limit)
            ->setFirstResult(($pageNumber - 1) * $limit)
        ;
        if ($whereBuild->count() > 0) {
            $qb->where($whereBuild);
            $qb->setParameters($whereParams);
        }
        $query = $qb->getQuery();

        $this->logger->info("----QUERY PRINT----");
        $this->logger->info($query->getDQL());
        $this->logger->info(print_r($query->getParameters(), true));
        $this->logger->info("----END QUERY PRINT----");

        $cards = new Paginator($query, $fetchJoinCollection = true);
        $resultCardCount = count($cards);

        $totalPagesNumber = intdiv($resultCardCount, $limit);
        if ($totalPagesNumber % $limit !== 0) {
            $totalPagesNumber += 1;
        } elseif ($totalPagesNumber < $limit) {
            $totalPagesNumber = 1;
        }

        $pagesNavigation = [1];
        if ($totalPagesNumber > 1) {
            $pagesNavigation = array_merge(
                $pagesNavigation,
                range(
                    max([$pageNumber - 3, 2]), min([$pageNumber + 3, $totalPagesNumber])
                )
            );
            if (! in_array($totalPagesNumber, $pagesNavigation)) {
                $pagesNavigation[] = $totalPagesNumber;
            }
        }

        return $this->render('cards/index.html.twig', [
            "searcheableFields" => [
                "sets" => $sets,
                "setTree" => $setTree,
                "rarities" => $rarities,
                "colors" => $colors
            ],
            "searchFilters" => $searchFilters,
            "headers" => $headers,
            "cards" => $cards,
            "pageInfos" => [
                "limit" => $limit,
                "limitsAvailable" => $limitsAvailable,
                "resultCardCount" => $resultCardCount,
                "pageCardCount" => $cards->getIterator()->count(),
                "pageNumber" => $pageNumber,
                "pagesNavigation" => $pagesNavigation,
            ]
        ]);
    }
}