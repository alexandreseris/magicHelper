<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use App\Service\Crawler;
use JsonMachine\JsonMachine;
use Doctrine\ORM\EntityManagerInterface;

class ScryfallDataGetter
{
    private string $scryfallTestData;
    private bool $scryfallFileDlSkip;
    private string $scryfallApiUrl;
    private string $scryfallApiDocumentationUrl;
    private string $scryfallDateTimeFormat;
    private int $scryfallWaitSecondsBetweenCalls;

    private HttpClientInterface $httpclient;
    private Crawler $htmlparser;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        string $scryfallTestData,
        bool $scryfallFileDlSkip,
        string $scryfallApiUrl,
        string $scryfallApiDocumentationUrl,
        string $scryfallDateTimeFormat,
        int $scryfallWaitSecondsBetweenCalls,

        LoggerInterface $logger,
        HttpClientInterface $httpclient,
        Crawler $htmlparser,
        EntityManagerInterface $entityManager
    ) {
        $this->scryfallTestData = $scryfallTestData;
        $this->scryfallFileDlSkip = $scryfallFileDlSkip;
        $this->scryfallApiUrl = $scryfallApiUrl;
        $this->scryfallApiDocumentationUrl = $scryfallApiDocumentationUrl;
        $this->scryfallDateTimeFormat = $scryfallDateTimeFormat;
        $this->scryfallWaitSecondsBetweenCalls = $scryfallWaitSecondsBetweenCalls;

        $this->logger = $logger;
        $this->httpclient = $httpclient;
        $this->htmlparser = $htmlparser;
        $this->entityManager = $entityManager;
    }

    /**
     * utilitaty function used for scrap data from the scryfall api documentation.
     */
    private function getDataFromDoc(string $url, int $tableIndex, array $lines): array
    {
        $response = $this->httpclient->request('GET', $url);
        $responseContent = $response->getContent();
        /** @var Crawler $crawler */
        $crawler = new $this->htmlparser($responseContent);

        sleep($this->scryfallWaitSecondsBetweenCalls);

        return $crawler->filter('.prose table tbody')->eq($tableIndex)->children('tr')->each(
            function (Crawler $node, $i) use ($lines) {
                $lineParsed = [];
                foreach ($lines as $line) {
                    $columnValue = $node->filter($line[0])->eq($line[1])->text();
                    $lineParsed[] = $columnValue;
                }

                return $lineParsed;
            }
        );
    }

    /**
     * (api documentation) https://scryfall.com/docs/api/colors.
     *
     * return an array of arrays for existings colors with:
     *     abreviation, friendly name and mana symbol.
     */
    public function getColorsData(): array
    {
        return $this->getDataFromDoc($this->scryfallApiDocumentationUrl . '/colors', 0, [
            ['td p code', 0], // abreviation
            ['td p', 1], // friendly name
            ['td abbr', 0], // mana symbol
        ]);
    }

    /**
     * (api documentation) https://scryfall.com/docs/api/layouts.
     *
     * return an array of arrays for existings layouts with:
     *     code and description.
     */
    public function getLayoutData(): array
    {
        return $this->getDataFromDoc($this->scryfallApiDocumentationUrl . '/layouts', 0, [
            ['td p code', 0], // name/code
            ['td p', 1], // description
        ]);
    }

    /**
     * (api documentation) https://scryfall.com/docs/api/sets.
     *
     * return an array of arrays for existings types of set with:
     *     code and description.
     */
    public function getSet_TypeData(): array
    {
        return $this->getDataFromDoc($this->scryfallApiDocumentationUrl . '/sets', 1, [
            ['td p code', 0], // name/code
            ['td p', 1], // description
        ]);
    }

    /**
     * doc: https://scryfall.com/docs/api/card-symbols.
     */
    public function getSymbolData(): array
    {
        $response = $this->httpclient->request('GET', $this->scryfallApiUrl . '/symbology');
        sleep($this->scryfallWaitSecondsBetweenCalls);
        return $response->toArray()['data'];
    }

    /**
     * doc: https://scryfall.com/docs/api/catalogs/keyword-actions and https://scryfall.com/docs/api/catalogs/keyword-abilities
     * return an array with keyword name as key and isAbility, isAction as values
     */
    public function getKeywordData(): array
    {
        $mergedData = [];

        $response = $this->httpclient->request('GET', $this->scryfallApiUrl . '/catalog/keyword-abilities');
        foreach ($response->toArray()['data'] as $keywordName) {
            $mergedData[$keywordName] = ["isAbility" => true, "isAction" => false];
        }

        $response = $this->httpclient->request('GET', $this->scryfallApiUrl . '/catalog/keyword-actions');
        foreach ($response->toArray()['data'] as $actionName) {
            if (in_array($actionName, $mergedData)) {
                $mergedData[$actionName] = ["isAbility" => true, "isAction" => true];
            } else {
                $mergedData[$actionName] = ["isAbility" => false, "isAction" => true];
            }
        }
        sleep($this->scryfallWaitSecondsBetweenCalls);
        return $mergedData;
    }

    /**
     * doc: https://scryfall.com/docs/api/sets.
     */
    public function getSetData(): array
    {
        $response = $this->httpclient->request('GET', $this->scryfallApiUrl . '/sets');
        sleep($this->scryfallWaitSecondsBetweenCalls);
        return $response->toArray()['data'];
    }

    /**
     * doc: https://scryfall.com/docs/api/catalogs/artist-names.
     */
    public function getArtistData(): array
    {
        $response = $this->httpclient->request('GET', $this->scryfallApiUrl . '/catalog/artist-names');
        sleep($this->scryfallWaitSecondsBetweenCalls);
        return $response->toArray()['data'];
    }


    /**
     * main function of this service, checks if there is a need to update the data and if so, download the bulk data and return safe parsed json
     * @param string $bulkType the type of bulk to load (type is passed to the /bulk-data scryfall's ressource)
     */
    public function getCardData(string $bulkType)
    {
        if ($this->scryfallFileDlSkip === true) {
            $neddUpdate = true;
            $tmpFile = $this->scryfallTestData;
            $bulkDate = new Datetime();
            $this->logger->info("skipping dl, using " . $tmpFile . " instead");
        } else {
            $response = $this->httpclient->request("GET", $this->scryfallApiUrl . "/bulk-data");
            $content = $response->toArray();
            sleep($this->scryfallWaitSecondsBetweenCalls);
            $targetBulk = null;
            foreach($content["data"] as $line) {
                if ($line["type"] == $bulkType) {
                    $targetBulk = $line;
                    break;
                }
            }
            if (is_null($targetBulk)) {
                throw new \Exception("did not find " . $bulkType . " in scryfall's bulks available", 1);
            }
            $lastUpdate = $this->entityManager->getRepository(\App\Entity\DataDate::class)->findAll();
            $neddUpdate = false;
            if (count($lastUpdate) == 0) {
                $neddUpdate = true;
            }
            else {
                $lastUpdate = $lastUpdate[0];
                $bulkDate = DateTime::createFromFormat($this->scryfallDateTimeFormat, $line["updated_at"]);
                $dateDiff = $lastUpdate->getUpdatedAt()->diff($bulkDate);
                if ($dateDiff->invert == 0) {
                    $neddUpdate = true;
                }
            }
        }
        if ($neddUpdate === true) {

            if (! $this->scryfallFileDlSkip === true) {
                $this->logger->debug("update available, downloading file");
                $fileRequest = $this->httpclient->request("GET", $line["download_uri"]);
                if ($fileRequest->getStatusCode() >= 400) {
                    throw new \Exception("failled to download bulk file", 1);
                }
                sleep($this->scryfallWaitSecondsBetweenCalls);
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

            // memory safe Json parser cause the file can be pretty fatty
            $parsedFile = JsonMachine::fromFile($tmpFile);
            return [
                "parsedFile" => $parsedFile,
                "filePath" => $tmpFile,
                "bulkDate" => $bulkDate];
        }
        return [];
    }
}
