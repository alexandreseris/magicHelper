<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
        $limitAvailable = [20, 50, 100];
        $pageRangeDisplay = 5;

        // if the user specified a limit and != from session, save the limit in session, else take the session or default
        $limit = $request->query->getInt("limit", -1);
        $sessionLimit = $session->get("limit", -1);
        if ($limit === -1) {
            if ($sessionLimit === -1) {
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

        $qb = $this->entityManager->createQueryBuilder();

        $whereBuild = $qb->expr()->andX();
        $whereParams = [];

        if ($request->isMethod("GET")) {
            $requestParams = $session->get("searchFilters", []);
        } else {
            $requestParams = $request->request->all();
            $session->set("searchFilters", $requestParams);
        }

        $cardnameFilter = $requestParams['cardname'] ?? null;
        if (!is_null($cardnameFilter) && '' !== $cardnameFilter) {
            $cardnameFilter = '%'.$cardnameFilter.'%';
            $whereBuild->add($qb->expr()->like('LOWER(faces.name)', ':cardname'));
            $whereParams['cardname'] = strtolower($cardnameFilter);
        }
        $setsFilter = $requestParams['setcode'] ?? null;
        if (!is_null($setsFilter)) {
            $whereBuild->add($qb->expr()->in('setofcard.code', ':setcode'));
            $whereParams['setcode'] = $setsFilter;
        }
        $rarirtyFilter = $requestParams['rarity'] ?? null;
        if (!is_null($rarirtyFilter)) {
            $whereBuild->add($qb->expr()->in('rarity.name', ':rarity'));
            $whereParams['rarity'] = $rarirtyFilter;
        }
        $colorFilter = $requestParams['color'] ?? null;
        if (!is_null($colorFilter)) {
            $whereBuild->add($qb->expr()->in('color_identity.code', ':color'));
            $whereParams['color'] = $colorFilter;
        }


        $qb
            ->from(\App\Entity\Card::class, "card")
            ->join("card.set", "setofcard")
            ->join("card.rarity", "rarity")
            ->join("card.color_identity", "color_identity")
            ->join("color_identity.symbols", "symbols")
            ->join("card.faces", "faces")
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
        $cards = new Paginator($query, $fetchJoinCollection = true);
        $cardCount = count($cards);

        $result = [];
        /** @var \App\Entity\Card $card */
        foreach ($cards as $card) {
            $cardId = $card->getIdScryfall();
            $setName = $card->getSet()->getName();
            $setIcon = $card->getSet()->getIconLocal();

            $rarityName = $card->getRarity()->getName();
            $rarity = [
                "name" => $rarityName
            ];

            $cardColors = [];
            /** @var \App\Entity\Color $color */
            foreach ($card->getColorIdentity() as $color) {
                $colorName = $color->getName();
                /** @var \App\Entity\Symbol $symbol */
                foreach ($color->getSymbols() as $symbol) {}
                $cardColors[] = [
                    "name" => $colorName,
                    "symbol" => $symbol->getIconLocal()
                ];
            }

            $facesName = [];
            /** @var \App\Entity\Face $face */
            foreach ($card->getFaces() as $face) {
                $facesName[] = $face->getName();
            }
            $cardName = implode(" - ", $facesName);

            $result[] = [
                "id" => $cardId,
                "setname" => $setName,
                "seticon" => $setIcon,
                "rarity" => $rarity,
                "colors" => $cardColors,
                "name" => $cardName
            ];
        }

        // this probably could be simplified !
        $totalNumberPage = intdiv($cardCount, $limit);
        if ($totalNumberPage % $limit !== 0) {
            $totalNumberPage += 1;
        } elseif ($totalNumberPage < $limit) {
            $totalNumberPage = 1;
        }

        $firstPages = [];
        if ($pageNumber !== 1) {
            $firstPages[] = 1;
        }
        if ($pageNumber > 1 && $pageNumber <= $totalNumberPage) {
            if ($pageNumber > $pageRangeDisplay) {
                $firstPages = array_merge($firstPages, range($pageNumber - $pageRangeDisplay + 1, $pageNumber - 1));
            } else {
                $firstPages = range(max([$pageNumber - $pageRangeDisplay, 1]), $pageNumber - 1);
            }
            array_reverse($firstPages);
        }

        $lastPages = [];
        if ($pageNumber !== $totalNumberPage) {
            $lastPages[] = $totalNumberPage;
        }
        if ($pageNumber < $totalNumberPage) {
            if ($pageNumber < $totalNumberPage - $pageRangeDisplay) {
                $lastPages = array_merge(range($pageNumber + 1, $pageNumber + $pageRangeDisplay - 1), $lastPages);
            } else {
                $lastPages = range($pageNumber + 1, min([$pageNumber + $pageRangeDisplay, $totalNumberPage]));
            }
        }

        return $this->render('cards/index.html.twig', [
            "searcheableFields" => [
                "sets" => $sets,
                "setTree" => $setTree,
                "rarities" => $rarities,
                "colors" => $colors
            ],
            "headers" => $headers,
            "cards" => $result,
            "currentLimit" => $limit,
            "cardCount" => $cardCount,
            "currentCardCount" => count($result),
            "currentPage" => $pageNumber,
            "totalNumberPage" => $totalNumberPage,
            "firstPages" => $firstPages,
            "lastPages" => $lastPages,
            "limitAvailable" => $limitAvailable
        ]);
    }
}