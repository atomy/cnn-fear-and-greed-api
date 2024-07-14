<?php

declare(strict_types=1);

namespace Fearandgreed\Controller;

use App\Container;
use Fearandgreed\PageParser;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class IndexController
 *
 * @package App
 */
class IndexController
{
    /**
     * @var Container
     */
    private Container $container;

    /**
     * HealthController constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get fear-and-greed-index from page and parse it.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \JsonException
     */
    public function get(Request $request, Response $response): Response
    {
        $this->container->getLogger()->info('Requesting fear-and-greed-index from page...');

        try {
            $indexDescription = (new PageParser($this->container->getPageClient()->get()))->getIndex();
        } catch (\Exception $exception) {
            $this->container->getLogger()->info('Requesting fear-and-greed-index from page... ERROR');
            $response->getBody()->write(json_encode(['errors' => ['message' => $exception]], JSON_THROW_ON_ERROR, 512));
            return $response->withStatus(500);
        }

        $response->getBody()->write(json_encode(['all' => $indexDescription], JSON_THROW_ON_ERROR, 512));
        $response = $response->withStatus(200);

        $this->container->getLogger()->info('Requesting fear-and-greed-index from page... DONE');

        return $response;
    }
}
