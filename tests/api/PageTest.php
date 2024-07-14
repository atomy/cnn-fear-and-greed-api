<?php

namespace Fearandgreed;

use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Factory as ClientFactory;
use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    /**
     * @var Phiremock
     */
    private Phiremock $phiremock;

    /**
     * Prepare testcase.
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->phiremock = ClientFactory::createDefault()->createPhiremockClient($this->getPhiremockHost(), $this->getPhiremockPort());
        $this->phiremock->reset();

        $expectation = Phiremock::on(
            A::getRequest()->andUrl(Is::containing('/'))
        )->then(
            Respond::withStatusCode(200)
                ->andBody(file_get_contents(ROOT_PATH . '/tests/data/api-result.json'))
                ->andHeader('Content-Type', 'application/json')
        );

        $this->phiremock->createExpectation($expectation);
    }

    /**
     * Issue a GET /index.
     *
     * @throws \JsonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetIndex(): void
    {
        $client = new Client(['base_uri' => 'http://cnn-fear-and-greed-nginx', 'headers' => ['User-Agent' => \App\EnvHelper::PHPUNIT_USER_AGENT]]);
        $response = $client->request('GET', '/index');
        $bodyContent = $response->getBody()->getContents();

        self::assertSame(200, $response->getStatusCode());
        self::assertNotNull($bodyContent);
        self::assertNotNull(json_decode($bodyContent, true), $bodyContent);
        self::assertSame(['all' => [
                '56 (Greed)',
                'Previous Close: 52 (Neutral)',
                '1 Week Ago: 53 (Neutral)',
                '1 Month Ago: 46 (Neutral)',
                '1 Year Ago: 78 (Extreme Greed)'
            ],
        ],
            json_decode($bodyContent, true, 512, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @return Host
     */
    private function getPhiremockHost(): Host
    {
        return new Host('cnn-fear-and-greed-phiremock');
    }

    /**
     * @return Port
     */
    private function getPhiremockPort(): Port
    {
        return new Port('80');
    }
}
