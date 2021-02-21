<?php

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Scryfall
{
    /** @var HttpClientInterface $httpclient */
    private $httpclient;

    public function __construct(HttpClientInterface $httpclient)
    {
        $this->$httpclient = $httpclient;
    }

    public function getBulkData($bulkType)
    {
        $response = $this->httpclient->request(
            "GET",
            "https://api.scryfall.com/bulk-data"
        );
        $content = $response->toArray();
        $targetBulk = null;
        foreach($content["data"] as $line) {
            if ($line["type" == $bulkType]) {
                $targetBulk = $line;
            }
        }
        if ($targetBulk == null) {
            throw new ErrorException("did not find " . $bulkType . " in scryfall bulks available");
        }
    }
}