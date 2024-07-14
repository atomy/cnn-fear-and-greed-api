<?php

declare(strict_types=1);

namespace Fearandgreed;

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
    private string $jsonContent;

    /**
     * PageParser constructor.
     *
     * @param string $jsonContent
     */
    public function __construct(string $jsonContent)
    {
        $this->jsonContent = $jsonContent;
    }

    /**
     * Parse html and return.
     *
     * @return array
     */
    public function getIndex(): array
    {
        $jsonObject = json_decode($this->jsonContent, true);

        if (empty($jsonObject)) {
            throw new \RuntimeException('Unable to parse json content!');
        }

        if (empty($jsonObject['fear_and_greed']['score']) || empty($jsonObject['fear_and_greed']['rating'])) {
            throw new \RuntimeException('Missing fields in json-response!');
        }

        $descriptions = [];

        // Build current.
        $score = round($jsonObject['fear_and_greed']['score']);
        $rating = ucwords($jsonObject['fear_and_greed']['rating']);
        $descriptions[] = sprintf('%d (%s)', $score, $rating);

        // Build previous-close.
        $score = (int) round($jsonObject['fear_and_greed']['previous_close']);
        $rating = $this->getWordingForScore($score);
        $descriptions[] = sprintf('Previous Close: %d (%s)', $score, $rating);

        // Build previous-1-week.
        $score = (int) round($jsonObject['fear_and_greed']['previous_1_week']);
        $rating = $this->getWordingForScore($score);
        $descriptions[] = sprintf('1 Week Ago: %d (%s)', $score, $rating);

        // Build previous-1-month.
        $score = (int) round($jsonObject['fear_and_greed']['previous_1_month']);
        $rating = $this->getWordingForScore($score);
        $descriptions[] = sprintf('1 Month Ago: %d (%s)', $score, $rating);

        // Build previous-1-year.
        $score = (int) round($jsonObject['fear_and_greed']['previous_1_year']);
        $rating = $this->getWordingForScore($score);
        $descriptions[] = sprintf('1 Year Ago: %d (%s)', $score, $rating);

        return $descriptions;
    }

    /**
     * Return wording for given input score.
     *
     * @param int $score input score
     * @return string
     */
    private function getWordingForScore(int $score): string
    {
        if ($score > 75) {
            return "Extreme Greed";
        }

        if ($score > 55) {
            return "Greed";
        }

        if ($score > 45) {
            return "Neutral";
        }

        if ($score > 25) {
            return "Fear";
        }

        return "Extreme Fear";
    }
}
