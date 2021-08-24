<?php

declare(strict_types=1);

namespace Fearandgreed;

use PHPHtmlParser\Dom;

/**
 * Class PageParser
 *
 * @package App
 */
class PageParser
{
    /**
     * @var string
     */
    private string $htmlContent;

    /**
     * PageParser constructor.
     *
     * @param string $htmlContent
     */
    public function __construct(string $htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    /**
     * Parse html and return.
     *
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function getIndex(): array
    {
        $dom = new Dom;
        $dom->load($this->htmlContent);
        $htmlCharts = $dom->find('#needleChart ul li');
        $descriptions = [];

        if (count($htmlCharts) > 0) {
            /** @var \PHPHtmlParser\Dom\HtmlNode $htmlChart */
            foreach ($htmlCharts as $htmlChart) {
                $description = (string) $htmlChart;

                $description = str_replace(array('<li>', '</li>', 'Fear &amp; Greed ', 'Now: '), '', $description);
                $descriptions[] = $description;
            }
        }

        return $descriptions;
    }
}
