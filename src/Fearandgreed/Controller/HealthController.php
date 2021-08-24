<?php

declare(strict_types=1);

namespace Fearandgreed\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class HealthController
 *
 * @package App
 */
class HealthController
{
    /**
     * Serving GET /health endpoint.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws GuzzleException
     */
    public function health(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write(json_encode(['result' => 'okay'], JSON_THROW_ON_ERROR, 512));
        $response = $response->withStatus(200);
        return $response;
    }
}
