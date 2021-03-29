<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class ScryfallUpdates {
    private string $scryfallDateTimeFormat;
    private string $scryfallDateFormat;
    private array $tablesNotScryfall;
    private bool $scryfallFileDlSkip;
    private int $batchSize;
    private string $scryfallBulkType;

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private ScryfallDataGetter $scryfallDataGetter;

    private array $cache;

    public function __construct(
        string $scryfallDateTimeFormat,
        string $scryfallDateFormat,
        array $tablesNotScryfall,
        bool $scryfallFileDlSkip,
        int $batchSize,
        string $scryfallBulkType,

        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        ScryfallDataGetter $scryfallDataGetter
    ) {
        $this->scryfallDateTimeFormat = $scryfallDateTimeFormat;
        $this->scryfallDateFormat = $scryfallDateFormat;
        $this->tablesNotScryfall = $tablesNotScryfall;
        $this->scryfallFileDlSkip = $scryfallFileDlSkip;
        $this->batchSize = $batchSize;
        $this->scryfallBulkType = $scryfallBulkType;

        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->scryfallDataGetter = $scryfallDataGetter;

        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->cache = [];
    }

    /**
     * utilitary function to help building cache easier
     * @param string $cacheKey is the key used to store the values in the cache property
     * @param string $entityClassName is the name of the class to query
     * @param string $entityGetter is the property's getter you wish to index
     */
    private function buildCacheItem(string $cacheKey, string $entityClassName, string $entityGetter) {
        $this->cache[$cacheKey] = [];
        foreach ($this->entityManager->getRepository($entityClassName)->findAll() as $data) {
            if ( ! is_null($data->{$entityGetter}()) ) {
                $this->cache[$cacheKey][$data->{$entityGetter}()] = $data;
            }
        }
    }

    /**
     * init cachable data, to help reduce the number of query on card update. 
     * cache is an array with table name as key.
     * values are array with key beein the data to index and values the doctrine obj
     */
    private function initCache() {
        $this->buildCacheItem("symbol", \App\Entity\Symbol::class, "getCode");
        $this->buildCacheItem("symbolVariant", \App\Entity\Symbol::class, "getCodeVariant");
        $this->buildCacheItem("color", \App\Entity\Color::class, "getCode");
        $this->buildCacheItem("set", \App\Entity\Set::class, "getCode");
        $this->buildCacheItem("layout", \App\Entity\Layout::class, "getCode");
        $this->buildCacheItem("artist", \App\Entity\Artist::class, "getName");
        $this->buildCacheItem("keyword", \App\Entity\Keyword::class, "getName");

        // the following can be updated manually inside the card loop
        $this->buildCacheItem("rarity", \App\Entity\Rarity::class, "getName");
        $this->buildCacheItem("legalityType", \App\Entity\LegalityType::class, "getName");
        $this->buildCacheItem("legalityValue", \App\Entity\LegalityValue::class, "getName");
    }

    /**
     * truncate tables containing scryfall data
     * 
     * currently I did not found a way to use DQL because of the join tables beeing not accessible this way so it plain sql based
     */
    private function truncateTables() {
        $tables = [];
        $entityTables = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($entityTables as $entityTable) {
            $tableName = $entityTable->table["name"];
            if (! in_array($tableName, $this->tablesNotScryfall, true)) {
                $tables[] = $tableName;
            }
            foreach ($entityTable->associationMappings as $associtation => $associtationMeta) {
                if (key_exists("joinTable", $associtationMeta)) {
                    $tableAssocationName = $associtationMeta["joinTable"]["name"];
                    if (! in_array($tableAssocationName, $this->tablesNotScryfall, true)) {
                        $tables[] = $tableAssocationName;
                    }
                }
            }
        }
        foreach ($tables as $table) {
            $this->entityManager->getConnection()->prepare("DELETE FROM " . $table)->execute();
        }
    }

    /**
     * function which insert the dependencies of the card object in the database
     */
    private function insertCardDependenciesData() {

        foreach ($this->scryfallDataGetter->getSymbolData() as $symbolData) {
            $symbol = new \App\Entity\Symbol();
            $symbol->setCode($symbolData["symbol"]);
            $symbol->setName($symbolData["english"]);
            if (key_exists("cmc", $symbolData)) {
                $symbol->setCmc($symbolData["cmc"]);
            }
            if (key_exists("loose_variant", $symbolData)) {
                $symbol->setCodeVariant($symbolData["loose_variant"]);
            }
            $symbol->setIconUrl($symbolData["svg_uri"]);
            $symbol->setIsFunny($symbolData["funny"]);
            $symbol->setIsMana($symbolData["appears_in_mana_costs"]);
            $this->entityManager->persist($symbol);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getColorsData() as $colorData) {
            $color = new \App\Entity\Color();
            $color->setCode($colorData[0]);
            $color->setName($colorData[1]);
            $symbol = $this->entityManager->getRepository(\App\Entity\Symbol::class)->findOneBy(["code" => $colorData[2]]);
            $color->addSymbol($symbol);
            $this->entityManager->persist($color);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getSet_TypeData() as $setTypeData) {
            $setType = new \App\Entity\SetType();
            $setType->setCode($setTypeData[0]);
            $setType->setName( ucfirst( str_replace("_", " ", $setTypeData[0]) ) );
            $setType->setDescription($setTypeData[1]);
            $this->entityManager->persist($setType);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getSetData() as $setData) {
            $set = new \App\Entity\Set();
            $set->setCode($setData["code"]);
            $set->setName($setData["name"]);
            $set->setIconUrl($setData["icon_svg_uri"]);
            if (key_exists("released_at", $setData)) {
                $set->setReleasedDate( DateTime::createFromFormat($this->scryfallDateFormat, $setData["released_at"]) );
            }
            $setType = $this->entityManager->getRepository(\App\Entity\SetType::class)->findOneBy(["code" => $setData["set_type"]]);
            $set->setType($setType);
            $this->entityManager->persist($set);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getLayoutData() as $layoutData) {
            $layout = new \App\Entity\Layout();
            $layout->setCode($layoutData[0]);
            $layout->setName( ucfirst( str_replace("_", " ", $layoutData[0]) ) );
            $layout->setDescription($layoutData[1]);
            $this->entityManager->persist($layout);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getArtistData() as $artistData) {
            $artist = new \App\Entity\Artist();
            $artist->setName($artistData);
            $this->entityManager->persist($artist);
        }
        $this->entityManager->flush();

        foreach ($this->scryfallDataGetter->getKeywordData() as $keywordName => $keywordTypes) {
            $keywoard = new \App\Entity\Keyword();
            $keywoard->setName($keywordName);
            $keywoard->setIsAbility($keywordTypes["isAbility"]);
            $keywoard->setIsAction($keywordTypes["isAction"]);
            $this->entityManager->persist($keywoard);
        }
        $this->entityManager->flush();
    }

    /**
     * function which insert card data
     * @param $scryfallCard is the scryfall representation of the card to insert 
     */
    private function insertCard(\App\Entity\ScryfallCard $scryfallCard) {
        // $this->logger->debug("updating card id " . $scryfallCard->id);

        $card = new \App\Entity\Card();
        $card->setIdScryfall($scryfallCard->id);
        $card->setIdOracle($scryfallCard->oracle_id);
        $card->setIdArena($scryfallCard->arena_id);
        $card->setReleasedDate(
            DateTime::createFromFormat($this->scryfallDateFormat, $scryfallCard->released_at)  
        );
        if (key_exists($scryfallCard->layout, $this->cache['layout'])) {
            $card->setLayout( $this->cache['layout'][$scryfallCard->layout] );
        }
        if (key_exists($scryfallCard->rarity, $this->cache['rarity'])) {
            $card->setRarity($this->cache['rarity'][$scryfallCard->rarity]);
        }
        if (key_exists($scryfallCard->set, $this->cache['set'])) {
            $card->setSet($this->cache['set'][$scryfallCard->set]);
        }
        foreach ($scryfallCard->color_identity as $color_identity) {
            if (key_exists($color_identity, $this->cache['color'])) {
                $card->addColorIdentity($this->cache['color'][$color_identity]);
            }
        }
        if ( ! is_null($scryfallCard->produced_mana) ) {
            foreach ($scryfallCard->produced_mana as $produced_mana) {
                if (key_exists($produced_mana, $this->cache['symbolVariant'])) {
                    $card->addProducedMana($this->cache['symbolVariant'][$produced_mana]);
                }
            }
        }
        foreach ($scryfallCard->keywords as $keyword) {
            if (key_exists($keyword, $this->cache['keyword'])) {
                $card->addKeyword($this->cache['keyword'][$keyword]);
            }
        }

        $this->entityManager->persist($card);

        foreach ($scryfallCard->legalities as $legalityType => $legalityValue) {
            if (key_exists($legalityType, $this->cache['legalityType']) && key_exists($legalityValue, $this->cache['legalityValue'])) {
                $legality = new \App\Entity\CardLegality();
                $legality->setLegalityType($this->cache['legalityType'][$legalityType]);
                $legality->setLegalityValue($this->cache['legalityValue'][$legalityValue]);
                $legality->setCard($card);
                $this->entityManager->persist($legality);
            }
        }
        
        if ( ! is_null($scryfallCard->card_faces) ) {
            /** @var \App\Entity\ScryfallFace $faceScryfall */
            foreach ($scryfallCard->card_faces as $faceIndex => $faceScryfall) {
                $face = new \App\Entity\Face();
                $face->setCard($card);
                $face->setFaceIndex($faceIndex);
                if ( ! is_null($faceScryfall->image_uris) && key_exists("normal", $faceScryfall->image_uris) ) {
                    $face->setImageUrl($faceScryfall->image_uris["normal"]);
                }
                $face->setName($faceScryfall->name);
                $face->setTypeLine($faceScryfall->type_line);
                $face->setOracleText($faceScryfall->oracle_text);
                $face->setPrintedText($faceScryfall->printed_text);
                $face->setPowerValue($faceScryfall->power);
                $face->setToughnessValue($faceScryfall->toughness);
                if (key_exists($faceScryfall->artist, $this->cache['artist'])) {
                    $face->setArtist($this->cache['artist'][$faceScryfall->artist]);
                }
                if ( ! is_null($faceScryfall->colors) ) {
                    foreach ($faceScryfall->colors as $color) {
                        if (key_exists($color, $this->cache['color'])) {
                            $face->addColor($this->cache['color'][$color]);
                        }
                    }
                }
                // manual split to do, cause scryfall return a string!
                // if ( ! is_null($faceScryfall->mana_cost) && $faceScryfall->mana_cost !== "" ) {
                //     foreach ($faceScryfall->mana_cost as $mana_cost) {
                //         if (key_exists($mana_cost, $this->cache['symbol'])) {
                //             $face->addManaCost($this->cache['symbol'][$mana_cost]);
                //         }
                //     }
                // }
                $this->entityManager->persist($face);
            }
        }
    }

    /**
     * main function of this service, update tables if fresh data is available
     */
    public function updateData()
    {
        $dataGetterReturn = $this->scryfallDataGetter->getCardData($this->scryfallBulkType);
        if (count($dataGetterReturn) === 0) {
            return;
        }

        $parsedFile = $dataGetterReturn["parsedFile"];
        $tmpFile = $dataGetterReturn["filePath"];
        $bulkDate = $dataGetterReturn["bulkDate"];
        $docSize = filesize($tmpFile);

        // disable if needed to speed up tests
        // $this->truncateTables();
        // $this->insertCardDependenciesData();
        $this->initCache();

        $lastProgress = 0;
        $numberOfCardsTreated = 0;
        $this->logger->info("progression: " . strval($lastProgress) . "%, " . strval($numberOfCardsTreated) . " cards treated");
        foreach ($parsedFile as $key => $data) {
            $currentProgress = intval($parsedFile->getPosition() / $docSize * 100);
            if ($currentProgress > $lastProgress && $currentProgress % 10 === 0) {
                $lastProgress = $currentProgress;
                $this->logger->info("progression: " . strval($lastProgress) . "%, " . strval($numberOfCardsTreated) . " cards treated");
            }

            $scryfallCardObj = new \App\Entity\ScryfallCard($data);

            // manually adding legalities and rarity values before card inserts, cause not available through API
            if (! key_exists($scryfallCardObj->rarity, $this->cache["rarity"]) ) {
                $rarityObj = new \App\Entity\Rarity();
                $rarityObj->setName($scryfallCardObj->rarity);
                $this->entityManager->persist($rarityObj);
                $this->cache["rarity"][$scryfallCardObj->rarity] = $rarityObj;
            }
            foreach ($scryfallCardObj->legalities as $legality_type => $legality_value) {
                if (! key_exists($legality_type, $this->cache["legalityType"]) ) {
                    $legalityTypeObj = new \App\Entity\LegalityType();
                    $legalityTypeObj->setName($legality_type);
                    $this->entityManager->persist($legalityTypeObj);
                    $this->cache["legalityType"][$legality_type] = $legalityTypeObj;
                }
                if (! key_exists($legality_value, $this->cache["legalityValue"]) ) {
                    $legalityValueObj = new \App\Entity\LegalityValue();
                    $legalityValueObj->setName($legality_value);
                    $this->entityManager->persist($legalityValueObj);
                    $this->cache["legalityValue"][$legality_value] = $legalityValueObj;
                }
            }
            $this->insertCard($scryfallCardObj);

            if ($numberOfCardsTreated % $this->batchSize === 0) {
                $this->entityManager->flush();
                // clearing from memory lines alredy inserted. can't call clear with no param as some data are stored as cache and would become unusable
                $this->entityManager->getUnitOfWork()->clear(\App\Entity\Face::class);
                $this->entityManager->getUnitOfWork()->clear(\App\Entity\CardLegality::class);
                $this->entityManager->getUnitOfWork()->clear(\App\Entity\Card::class);
            }

            $numberOfCardsTreated += 1;
        }
        $this->entityManager->flush();
        $this->logger->info("progression: 100%, " . strval($numberOfCardsTreated) . " cards treated");

        // $lastUpdate = $this->entityManager->getRepository(\App\Entity\DataDate::class)->findAll();
        // if (count($lastUpdate) == 0) {
        //     $currentUpdate = new \App\Entity\DataDate();
        //     $currentUpdate->setUpdatedAt($bulkDate);
        //     $this->entityManager->persist($currentUpdate);
        // } else {
        //     $lastUpdate[0]->setUpdatedAt($bulkDate);
        // }
        // $this->entityManager->flush();

        if (! $this->scryfallFileDlSkip === true) {
            unlink($tmpFile);
        }
    }
}
