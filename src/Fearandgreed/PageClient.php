<?php

declare(strict_types=1);

namespace Fearandgreed;

use App\Container;
use App\EnvHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;

/**
 * Class PageClient
 *
 * @package App
 */
class PageClient
{
    /**
     * @var Container
     */
    private Container $container;

    /**
     * PageClient constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
     }

    /**
     * Access cnn fear and greed data and returns it's json string.
     */
    public function get(): string
    {
        $currentDateTime = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $currentDateTimeFormatted = $currentDateTime->format('Y-m-d');

        $dataUrl = EnvHelper::get('CNN_SITE') . $currentDateTimeFormatted;

        try {
            $client = new Client();
            $this->container->getLogger()->info(' Issuing GET ' . $dataUrl . ' ...');
            // Make the request with the specified User-Agent
            $response = $client->request('GET', $dataUrl, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
                    'Referer' => 'https://edition.cnn.com/',
                    'Origin' => 'https://edition.cnn.com',
                ]
            ]);
            $responseBody = $response->getBody()->getContents();

            if (200 === $response->getStatusCode()) {
                $this->container->getLogger()->info(' Issuing GET ' . $dataUrl . ' ... SUCCESS');
                return $responseBody;
            }

            $this->container->getLogger()->error(' Issuing GET ' . $dataUrl . ' ... ERROR (' . $response->getStatusCode() . ')');
            throw new \RuntimeException('Failed to retrieve CNN_SITE-page');
        } catch (GuzzleException $guzzleException) {
            $this->container->getLogger()->info(' Issuing GET ' . $dataUrl . ' ... ERROR (' . $guzzleException->getMessage() . ')');
            /** @var Logger $monolog */
            $monolog = $this->container->getLogger();
            $monolog->error($guzzleException);
            throw new \RuntimeException('Failed to retrieve CNN_SITE-page: ' . $guzzleException->getMessage());
        }
    }
}
