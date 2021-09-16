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
            A::getRequest()->andUrl(Is::equalTo('/'))
        )->then(
            Respond::withStatusCode(200)
                ->andBody(file_get_contents(ROOT_PATH . '/tests/data/page.html'))
                ->andHeader('Content-Type', 'text/html')
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
                '25 (Extreme Fear)',
                'Previous Close: 22 (Extreme Fear)',
                '1 Week Ago : 43 (Fear)',
                '1 Month Ago: 25 (Extreme Fear)',
                '1 Year Ago: 70 (Greed)'
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
