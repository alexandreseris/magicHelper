<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class HttpClient
{
    private int $scryfallWaitSecondsBetweenCalls;
    private HttpClientInterface $httpclient;
    private LoggerInterface $logger;

    public function __construct(
        int $scryfallWaitSecondsBetweenCalls,
        LoggerInterface $logger,
        HttpClientInterface $httpclient
    ) {
        $this->scryfallWaitSecondsBetweenCalls = $scryfallWaitSecondsBetweenCalls;
        $this->logger = $logger;
        $this->httpclient = $httpclient;
    }

    /**
     * utilitary function to download file using http client
     * @param string $remoteUrl the remote url you wish to download
     * @param string $localFile the local path to save your file
     * @param bool $temporaryFile = false pass true if you wish to save as temp file. You should just pass the filename (and not its path) if true
     * @return $localFile can be used later, specially if using the $temporaryFile flag
     */
    public function downloadFile(string $remoteUrl, string $localFile, bool $temporaryFile = false): string
    {
        $fileRequest = $this->httpclient->request("GET", $remoteUrl);
        if ($fileRequest->getStatusCode() >= 400) {
            throw new \Exception("failled to download file " . $remoteUrl, 1);
        }
        sleep($this->scryfallWaitSecondsBetweenCalls);
        if ($temporaryFile) {
            $localFile = tempnam(sys_get_temp_dir(), $localFile);
        }
        if ($localFile) {
            $this->logger->debug('saving to '.$localFile);
            $fileHandler = fopen($localFile, 'w');
            foreach ($this->httpclient->stream($fileRequest) as $chunk) {
                fwrite($fileHandler, $chunk->getContent());
            }
            fclose($fileHandler);
        }
        else {
            throw new \Exception("failed to create file " . $localFile, 1);   
        }
        return $localFile;
    }
}
