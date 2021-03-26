<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use App\Service\Crawler;
use Doctrine\ORM\EntityManager;
use JsonMachine\JsonMachine;
use Doctrine\ORM\EntityManagerInterface;

class Scryfall
{
    public static string $apiBaseUrl = "https://api.scryfall.com";
    public static string $apiDocBaseUrl = "https://scryfall.com/docs/api";

    public static string $dateTimeFormat = "Y-m-d\Th:i:s.vP";
    public static string $dateFormat = "Y-m-d";
    public static int $waitSecondsBetweenCalls = 2;

    private HttpClientInterface $httpclient;
    private Crawler $htmlparser;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        string $scryfallTestData,
        bool $scryfallFileDlSkip,
        LoggerInterface $logger,
        HttpClientInterface $httpclient,
        Crawler $htmlparser,
        EntityManagerInterface $entityManager
    ) {
        $this->scryfallTestData = $scryfallTestData;
        $this->scryfallFileDlSkip = $scryfallFileDlSkip;
        $this->logger = $logger;
        $this->httpclient = $httpclient;
        $this->htmlparser = $htmlparser;
        $this->entityManager = $entityManager;
    }

    /**
     * build properties of card object based on the api documentation.
     * Used manually for updating/creating classes representing card and associated from the scryfall perspective
     * this methode is not 100% accurate as it depends on the reliability of the scryfall doc. you may need to update classes by yourself :|
     */
    public function getCardSchemaFromDoc(): string
    {
        $documentationTypeMapping = [
            "Integer" => "int",
            "UUID" => "string",
            "String" => "string",
            "Array" => "array",
            "URI" => "string",
            "Decimal" => "float",
            "Colors" => "array",
            "Boolean" => "bool",
            "Object" => "array",
            "Date" => "string"
        ];
        $response = $this->httpclient->request("GET", self::$apiDocBaseUrl . "/cards");
        $responseContent = $response->getContent();
        /** @var Crawler $crawler */
        $crawler = new $this->htmlparser($responseContent);
        $mainSection = $crawler->filter(".prose")->eq(0);
        $dataTableNodes = $mainSection->filter("table tbody"); // properties tables (name, type and nullable)
        $titleTable = $mainSection->filter("h2")->each( // title of the properties tables
            function(Crawler $node, $i){
                return $node->attr("id");
            }
        );
        $titleTable = array_slice($titleTable, count($titleTable) - $dataTableNodes->count()); // remove titles with no table attached. Depends on the fact that only the firsts titles have no table bellow
        $dataTable = $dataTableNodes->each(
            function(Crawler $tableNode, $i) use ($titleTable, $documentationTypeMapping){
                $propertiesLines = $tableNode->filter("tr")->each(
                    function(Crawler $lineNode, $i) use ($documentationTypeMapping){
                        $propertyName = $lineNode->filter("td code")->eq(0)->text();
                        $propertyType = $lineNode->filter("td")->eq(1)->text();
                        $propertyNullable = $lineNode->filter("td span")->count();
                        $nullable = false;
                        if ($propertyNullable > 0) {
                            $nullable = true;
                        }
                        return [$propertyName, $documentationTypeMapping[$propertyType], $nullable];
                    }
                );
                return [$titleTable[$i], $propertiesLines];
            }
        );
        // cleaning some properties
        foreach ($dataTable as &$dataType) {
            $typeLib = $dataType[0];
            $props = &$dataType[1];
            foreach ($props as &$prop) {
                $propName = $prop[0];
                $propType = $prop[1];
                $propNullable = $prop[2];
                if (str_contains($propName , ".")) {
                    $newNameProp = explode(".", $propName)[0];
                    $newProp = [$newNameProp, "array", $propNullable];
                    if (! in_array($newProp, $props)) {
                        $props[] = $newProp;
                    }
                    unset($props[array_search($prop, $props)]);
                }
            }
            unset($prop);
        }
        unset($dataType);
        unset($prop);
        unset($props);

        $strBuild = "";
        foreach ($dataTable as $dataType) {
            $typeLib = $dataType[0];
            $props = $dataType[1];
            $strBuild = $strBuild . "// " . $typeLib . "\n";
            foreach ($props as $prop) {
                $propName = $prop[0];
                $propType = $prop[1];
                $propNullable = $prop[2];
                $strBuild = $strBuild . "public ";
                if ($propNullable) {
                    $strBuild = $strBuild . "?";
                }
                $strBuild = $strBuild . $propType . " $" . $propName . ";\n";
            }
        }
        return $strBuild;
    }

    /**
     * utilitaty function used for scrap data from the scryfall api documentation
     */
    private function getDataFromDoc(string $url, int $tableIndex, array $lines): array
    {
        $response = $this->httpclient->request("GET", $url);
        $responseContent = $response->getContent();
        /** @var Crawler $crawler */
        $crawler = new $this->htmlparser($responseContent);
        $data = $crawler->filter(".prose table tbody")->eq($tableIndex)->children("tr")->each(
            function(Crawler $node, $i) use ($lines)
            {
                $lineParsed = [];
                foreach ($lines as $line) {
                    $columnValue = $node->filter($line[0])->eq($line[1])->text();
                    $lineParsed[] = $columnValue;
                }
                return $lineParsed;
        });
        return $data;
    }


    /**
     * (api documentation) https://scryfall.com/docs/api/colors
     * 
     * return an array of arrays for existings colors with:
     *     abreviation, friendly name and mana symbol.
     */
    private function getColorsData(): array
    {
        return $this->getDataFromDoc(self::$apiDocBaseUrl . "/colors", 0, [
            ["td p code", 0], // abreviation
            ["td p", 1], // friendly name
            ["td abbr", 0] // mana symbol
        ]);
    }

    /**
     * (api documentation) https://scryfall.com/docs/api/layouts
     * 
     * return an array of arrays for existings layouts with:
     *     code and description.
     */
    private function getLayoutData(): array
    {
        return $this->getDataFromDoc(self::$apiDocBaseUrl . "/layouts", 0, [
            ["td p code", 0], // name/code
            ["td p", 1] // description
        ]);
    }

    /**
     * (api documentation) https://scryfall.com/docs/api/sets
     * 
     * return an array of arrays for existings types of set with:
     *     code and description.
     */
    private function getSet_TypeData(): array
    {
        return $this->getDataFromDoc(self::$apiDocBaseUrl . "/sets", 1, [
            ["td p code", 0], // name/code
            ["td p", 1] // description
        ]);
    }

    /**
     * doc: https://scryfall.com/docs/api/card-symbols
     */
    private function getSymbolData(): array {
        $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/symbology");
        return $response->toArray()["data"];
    }

    /**
     * doc: https://scryfall.com/docs/api/catalogs/keyword-actions and https://scryfall.com/docs/api/catalogs/keyword-abilities
     * array with keys beeing the type of keywords (abilities and actions) and values beeing the keywords
     */
    private function getKeywordData(): array {
        $mergedData = [];

        $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/catalog/keyword-abilities");
        $mergedData["abilities"] = $response->toArray()["data"];

        $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/catalog/keyword-actions");
        $mergedData["actions"] = $response->toArray()["data"];

        return $mergedData;
    }

    /**
     * doc: https://scryfall.com/docs/api/sets
     */
    private function getSetData(): array {
        $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/sets");
        return $response->toArray()["data"];
    }

    /**
     * doc: https://scryfall.com/docs/api/catalogs/artist-names
     */
    private function getArtistData(): array {
        $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/catalog/artist-names");
        return $response->toArray()["data"];
    }

    /**
     * function which insert the dependencies of the card object in the database
     */
    public function updateCardDependenciesData() {

        foreach ($this->getSymbolData() as $symbolData) {
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
        sleep(self::$waitSecondsBetweenCalls);

        foreach ($this->getColorsData() as $colorData) {
            $color = new \App\Entity\Color();
            $color->setCode($colorData[0]);
            $color->setName($colorData[1]);
            $symbol = $this->entityManager->getRepository(\App\Entity\Symbol::class)->findOneBy(["code" => $colorData[2]]);
            $color->addSymbol($symbol);
            $this->entityManager->persist($color);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);

        foreach ($this->getSet_TypeData() as $setTypeData) {
            $setType = new \App\Entity\SetType();
            $setType->setCode($setTypeData[0]);
            $setType->setName( ucfirst( str_replace("_", " ", $setTypeData[0]) ) );
            $setType->setDescription($setTypeData[1]);
            $this->entityManager->persist($setType);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);

        foreach ($this->getSetData() as $setData) {
            $set = new \App\Entity\Set();
            $set->setCode($setData["code"]);
            $set->setName($setData["name"]);
            if (key_exists("released_at", $setData)) {
                $set->setReleasedDate( DateTime::createFromFormat(self::$dateFormat, $setData["released_at"]) );
            }
            $setType = $this->entityManager->getRepository(\App\Entity\SetType::class)->findOneBy(["code" => $setData["set_type"]]);
            $set->setType($setType);
            $this->entityManager->persist($set);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);

        foreach ($this->getArtistData() as $artistData) {
            $artist = new \App\Entity\Artist();
            $artist->setName($artistData[0]);
            $this->entityManager->persist($artist);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);

        foreach ($this->getLayoutData() as $layoutData) {
            $layout = new \App\Entity\Layout();
            $layout->setCode($layoutData[0]);
            $layout->setName( ucfirst( str_replace("_", " ", $layoutData[0]) ) );
            $layout->setDescription($layoutData[1]);
            $this->entityManager->persist($layout);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);

        $keywoards = $this->getKeywordData();
        foreach ($keywoards["abilities"] as $keywoard) {
            $keywoard = new \App\Entity\Keyword();
            $keywoard->setName($keywoard[0]);
            $keywoard->setIsAbility(true);
            $keywoard->setIsAction(false);
            $this->entityManager->persist($keywoard);
        }
        foreach ($keywoards["actions"] as $keywoard) {
            $keywoard = new \App\Entity\Keyword();
            $keywoard->setName($keywoard[0]);
            $keywoard->setIsAbility(false);
            $keywoard->setIsAction(true);
            $this->entityManager->persist($keywoard);
        }
        $this->entityManager->flush();
        sleep(self::$waitSecondsBetweenCalls);
    }

    /**
     * main function of this service, checks if there is a need to update the data and if so, do it safely
     * @param string $bulkType the type of bulk to load (type is passed to the /bulk-data scryfall's ressource)
     */
    public function updateData(string $bulkType)
    {
        if ($this->scryfallFileDlSkip === true) {
            $neddUpdate = true;
            $tmpFile = $this->scryfallTestData;
            $this->logger->info("skipping dl, using " . $tmpFile . " instead");
        } else {
            $response = $this->httpclient->request("GET", self::$apiBaseUrl . "/bulk-data");
            $content = $response->toArray();
            $targetBulk = null;
            foreach($content["data"] as $line) {
                if ($line["type"] == $bulkType) {
                    $targetBulk = $line;
                    break;
                }
            }
            if ($targetBulk == null) {
                throw new \Exception("did not find " . $bulkType . " in scryfall's bulks available", 1);
            }
            $lastUpdate = $this->entityManager->getRepository(\App\Entity\DataDate::class)->findAll();
            $neddUpdate = false;
            if (count($lastUpdate) == 0) {
                $neddUpdate = true;
            }
            else {
                $lastUpdate = $lastUpdate[0];
                $bulkDate = DateTime::createFromFormat(self::$dateTimeFormat, $line["updated_at"]);
                $dateDiff = $lastUpdate->getUpdatedAt()->diff($bulkDate);
                if ($dateDiff->invert == 0) {
                    $neddUpdate = true;
                }
            }
        }
        if ($neddUpdate === true) {
            $this->updateCardDependenciesData();

            if (! $this->scryfallFileDlSkip === true) {
                $this->logger->debug("update available, downloading file");
                $fileRequest = $this->httpclient->request("GET", $line["download_uri"]);
                if ($fileRequest->getStatusCode() >= 400) {
                    throw new \Exception("failled to download bulk file", 1);
                }
                $tmpFile = tempnam(sys_get_temp_dir(), "scryfallData.json");
                if ($tmpFile) {
                    $this->logger->debug('saving to '.$tmpFile);
                    $fileHandler = fopen($tmpFile, 'w');
                    foreach ($this->httpclient->stream($fileRequest) as $chunk) {
                        fwrite($fileHandler, $chunk->getContent());
                    }
                    fclose($fileHandler);
                }
                else {
                    throw new \Exception("failed to create temp file " . $tmpFile, 1);   
                }
            }

            $docSize = filesize($tmpFile);
            $this->logger->info("file saved, (size: " . $docSize . "o)");
            
            $rarities = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\Rarity::class)->findAll());
            $legality_types = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\LegalityType::class)->findAll());
            $legality_values = array_map(function($line) {return $line->getName();}, $this->entityManager->getRepository(\App\Entity\LegalityValue::class)->findAll());

            // memory safe Json parser cause the file can be pretty fatty
            $parsedFile = JsonMachine::fromFile($tmpFile);
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
                if (!in_array($rarity, $rarities)) {
                    $rarityObj = new \App\Entity\Rarity();
                    $rarityObj->setName($rarity);
                    $this->entityManager->persist($rarityObj);
                    $this->entityManager->flush();
                    $rarities[] = $rarity;
                }
                foreach ($data["legalities"] as $legality_type => $legality_value) {
                    if (!in_array($legality_type, $legality_types)) {
                        $legalityTypeObj = new \App\Entity\LegalityType();
                        $legalityTypeObj->setName($legality_type);
                        $this->entityManager->persist($legalityTypeObj);
                        $this->entityManager->flush();
                        $legality_types[] = $legality_type;
                    }
                    if (!in_array($legality_value, $legality_values)) {
                        $legalityValueObj = new \App\Entity\LegalityValue();
                        $legalityValueObj->setName($legality_value);
                        $this->entityManager->persist($legalityValueObj);
                        $this->entityManager->flush();
                        $legality_values[] = $legality_value;
                    }
                }
                // dependencies of card obj need to be inserted/updated first
                // entity representing the scryfall data and securing data structures (unknown types, wrong types, etc)
                $testMappingObj = new \App\Entity\ScryfallCard($data);
                // repo mapping the scryfall entity with doctrine, picking desired fields
                // making doctrine update if card present but changed else insert (maybe build in???)
            }
            $this->logger->info("progression: 100%");

            if (! $this->scryfallFileDlSkip === true) {
                unlink($tmpFile);
            }
        }
    }
}
