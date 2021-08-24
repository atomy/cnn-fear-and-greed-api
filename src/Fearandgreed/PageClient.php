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
     * Access cnn fear and greed page and return its html-content.
     */
    public function get(): string
    {
        try {
            $client = new Client();
            $this->container->getLogger()->info(' Issuing GET ' . EnvHelper::get('CNN_SITE') . ' ...');
            $response = $client->request('GET', EnvHelper::get('CNN_SITE'));

            $responseBody = $response->getBody()->getContents();

            if (200 === $response->getStatusCode()) {
                $this->container->getLogger()->info(' Issuing GET ' . EnvHelper::get('CNN_SITE') . ' ... SUCCESS');
                return $responseBody;
            }

            $this->container->getLogger()->error(' Issuing GET ' . EnvHelper::get('CNN_SITE') . ' ... ERROR (' . $response->getStatusCode() . ')');
            throw new \RuntimeException('Failed to retrieve CNN_SITE-page');
        } catch (GuzzleException $guzzleException) {
            $this->container->getLogger()->info(' Issuing GET ' . EnvHelper::get('CNN_SITE') . ' ... ERROR (' . $guzzleException->getMessage() . ')');
            /** @var Logger $monolog */
            $monolog = $this->container->getLogger();
            $monolog->error($guzzleException);
            throw new \RuntimeException('Failed to retrieve CNN_SITE-page: ' . $guzzleException->getMessage());
        }
    }
}
