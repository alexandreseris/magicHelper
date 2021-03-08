<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use App\Repository\DataDateRepository;
use App\Service\Crawler;
use JsonMachine\JsonMachine;

class Scryfall
{
    public static $dateTimeFormat = "Y-m-d\Th:i:s.vP";
    public static $dateFormat = "Y-m-d";

    private HttpClientInterface $httpclient;
    private Crawler $htmlparser;
    private DataDateRepository $dataDate;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, HttpClientInterface $httpclient, Crawler $htmlparser, DataDateRepository $dataDate)
    {
        $this->logger = $logger;
        $this->httpclient = $httpclient;
        $this->htmlparser = $htmlparser;
        $this->dataDate = $dataDate;
    }

    public function getCardSchemaFromDoc(): string // currently not used but could become handy to automate class building for the scryfall data model of card and associated
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
        $response = $this->httpclient->request("GET", "https://scryfall.com/docs/api/cards");
        $responseContent = $response->getContent();
        /** @var Crawler $crawler */
        $crawler = new $this->htmlparser($responseContent);
        $mainSection = $crawler->filter(".prose")->eq(0);
        $dataTableNodes = $mainSection->filter("table tbody"); // properties tables (name, type and nullable)
        $titleTable = $mainSection->filter("h2")->each( // title of the properties tables
            function(Crawler $node, $i){
                return $node->text();
            }
        );
        $titleTable = array_slice($titleTable, count($titleTable) - $dataTableNodes->count()); // remove titles with no table attached. Depends on the fact that only the firsts titles have no table
        $dataTable = $dataTableNodes->each(
            function(Crawler $tableNode, $i) use ($titleTable, $documentationTypeMapping){
                $propertiesLines = $tableNode->filter("tr")->each(
                    function(Crawler $lineNode, $i) use ($documentationTypeMapping){
                        $propertyName = $lineNode->filter("td code")->eq(0)->text();
                        $propertyType = $lineNode->filter("td")->eq(1)->text();
                        $propertyNullable = $lineNode->filter("td span")->count();
                        $strPropertyBuild = "public ";
                        if ($propertyNullable !== 0) {
                            $strPropertyBuild = $strPropertyBuild . "?";
                        }
                        $strPropertyBuild = "public " . $documentationTypeMapping[$propertyType] . " $" . $propertyName . ";";
                        return $strPropertyBuild;
                    }
                );
                return [$titleTable[$i], $propertiesLines];
            }
        );
        $strBuild = "";
        foreach ($dataTable as $dataType) {
            $strBuild = $strBuild . "// " . $dataType[0] . "\n";
            foreach ($dataType[1] as $prop) {
                $strBuild = $strBuild . $prop . "\n";
            }
        }
        return $strBuild;
    }

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

    public function getColorsData(): array
    {
        return $this->getDataFromDoc("https://scryfall.com/docs/api/colors", 0, [
            ["td p code", 0], // abreviation
            ["td p", 1], // friendly name
            ["td abbr", 0] // mana symbol
        ]);
    }

    public function getLayoutData(): array
    {
        return $this->getDataFromDoc("https://scryfall.com/docs/api/layouts", 0, [
            ["td p code", 0], // name/code
            ["td p", 1] // description
        ]);
    }

    public function getSet_TypeData(): array
    {
        return $this->getDataFromDoc("https://scryfall.com/docs/api/sets", 1, [
            ["td p code", 0], // name/code
            ["td p", 1] // description
        ]);
    }

    public function getBulkData($bulkType) //: array
    {
        $response = $this->httpclient->request("GET", "https://api.scryfall.com/bulk-data");
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
        $lastUpdate = $this->dataDate->findAll();
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
        if ($neddUpdate) {
            $this->logger->debug("update available, downloading file");
            $fileRequest = $this->httpclient->request("GET", $line["download_uri"]);
            if ($fileRequest->getStatusCode() >= 400) {
                throw new \Exception("failled to download bulk file", 1);
            }
            $tmpFile = tempnam(sys_get_temp_dir(), "scryfallData.json");
            if ($tmpFile) {
                $this->logger->debug('saving to '. $tmpFile);
                $fileHandler = fopen($tmpFile, 'w');
                foreach ($this->httpclient->stream($fileRequest) as $chunk) {
                    fwrite($fileHandler, $chunk->getContent());
                }
                fclose($fileHandler);

                $docSize = filesize($tmpFile);
                $this->logger->info("file saved, (size: " . $docSize . "o)");
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
                    // echo "VALUE" . print_r($data, true);
                }
                $this->logger->info("progression: 100%");

                unlink($tmpFile);
            }
            else {
                throw new \Exception("failed to create temp file " . $tmpFile, 1);
                
            }
        }
    }
}