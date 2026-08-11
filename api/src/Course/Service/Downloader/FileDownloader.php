<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

use App\Parser\Exception\RemoteException;
use DomainException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class FileDownloader implements FileDownloaderInterface
{
    public function __construct(
        private readonly HttpClientInterface $client
    ) {}

    public function download(string $url, string $destinationFilePath): void
    {
        $fileHandler = fopen($destinationFilePath, 'wb');

        if (!$fileHandler) {
            throw new DomainException(\sprintf('Не удалось открыть файл "%s" для записи', $destinationFilePath));
        }
        try {
            $response = $this->client->request('GET', $url, [
                'max_duration' => 30.0,
                'timeout'      => 5.0,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'     => '*/*',
                ],
            ]);
            if (200 !== $response->getStatusCode()) {
                throw new RemoteException('Server returned HTTP code ' . $response->getStatusCode());
            }
            foreach ($this->client->stream($response) as $chunk) {
                fwrite($fileHandler, $chunk->getContent());
            }
        } catch (Throwable $throwable) {
            if (\is_resource($fileHandler)) {
                fclose($fileHandler);
            }
            if (file_exists($destinationFilePath)) {
                unlink($destinationFilePath);
            }
            throw new RemoteException('Can not download image: ' . $throwable->getMessage());
        } finally {
            if (\is_resource($fileHandler)) {
                fclose($fileHandler);
            }
        }
    }
}
