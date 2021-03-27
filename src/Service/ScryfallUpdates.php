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
    private string $scryfallBulkType;

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private ScryfallDataGetter $scryfallDataGetter;


    public function __construct(
        string $scryfallDateTimeFormat,
        string $scryfallDateFormat,
        array $tablesNotScryfall,
        bool $scryfallFileDlSkip,
        string $scryfallBulkType,

        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        ScryfallDataGetter $scryfallDataGetter
    ) {
        $this->scryfallDateTimeFormat = $scryfallDateTimeFormat;
        $this->scryfallDateFormat = $scryfallDateFormat;
        $this->tablesNotScryfall = $tablesNotScryfall;
        $this->scryfallFileDlSkip = $scryfallFileDlSkip;
        $this->scryfallBulkType = $scryfallBulkType;

        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->scryfallDataGetter = $scryfallDataGetter;
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
    private function updateCardDependenciesData() {

        foreach ($this->scryfallDataGetter->getSymbolData() as $symbolData) {
            $symbol = new \App\Entity\Symbol();
            $symbol->setCode($symbolData["symbol"]);
            $symbol->setName($symbolData["english"]);
            if (key_exists("cmc", $symbolData)) {
                $symbol->setCmc($symbolData["cmc"]);
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
     * main function of this service, update tables if fresh data is available
     */
    public function updateData()
    {
        $dataGetterReturn = $this->scryfallDataGetter->getCardData($this->scryfallBulkType);
        if (count($dataGetterReturn) === 0) {
            return;
        }
        $parsedFile = $dataGetterReturn[0];
        $tmpFile = $dataGetterReturn[1];
        $docSize = filesize($tmpFile);

        $this->truncateTables();
        $this->updateCardDependenciesData();

        $rarities = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\Rarity::class)->findAll());
        $legality_types = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\LegalityType::class)->findAll());
        $legality_values = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\LegalityValue::class)->findAll());

        $lastProgress = 0;
        $this->logger->info("progression: " . strval($lastProgress) . "%");
        foreach ($parsedFile as $key => $data) {
            $currentProgress = intval($parsedFile->getPosition() / $docSize * 100);
            if ($currentProgress > $lastProgress) {
                $lastProgress = $currentProgress;
                $this->logger->info("progression: " . strval($lastProgress) . "%");
            }
            // manually adding legalities and rarity values before card inserts, cause not available through API
            $rarity = $data["rarity"];
            if (! in_array($rarity, $rarities)) {
                $rarityObj = new \App\Entity\Rarity();
                $rarityObj->setName($rarity);
                $this->entityManager->persist($rarityObj);
                $this->entityManager->flush();
                $rarities[] = $rarity;
            }
            foreach ($data["legalities"] as $legality_type => $legality_value) {
                if (! in_array($legality_type, $legality_types)) {
                    $legalityTypeObj = new \App\Entity\LegalityType();
                    $legalityTypeObj->setName($legality_type);
                    $this->entityManager->persist($legalityTypeObj);
                    $this->entityManager->flush();
                    $legality_types[] = $legality_type;
                }
                if (! in_array($legality_value, $legality_values)) {
                    $legalityValueObj = new \App\Entity\LegalityValue();
                    $legalityValueObj->setName($legality_value);
                    $this->entityManager->persist($legalityValueObj);
                    $this->entityManager->flush();
                    $legality_values[] = $legality_value;
                }
            }
            $testMappingObj = new \App\Entity\ScryfallCard($data);
            // left to do:
            // repo mapping the scryfall entity with doctrine, picking desired fields, and commiting data
        }
        $this->logger->info("progression: 100%");

        if (! $this->scryfallFileDlSkip === true) {
            unlink($tmpFile);
        }
    }
}
