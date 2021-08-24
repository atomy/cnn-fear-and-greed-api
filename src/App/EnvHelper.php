<?php

declare(strict_types=1);

namespace App;

/**
 * Class EnvHelper.
 *
 * @package App
 */
class EnvHelper
{
    public const PHPUNIT_USER_AGENT = 'phpunit23kl4j20fduadsf';

    /**
     * @param string $envKey
     * @return string
     */
    public static function get(string $envKey): string
    {
        if (empty($envKey)) {
            throw new \InvalidArgumentException('Missing arguments!');
        }

        $val = getenv($envKey);

        if (empty($val)) {
            throw new \RuntimeException(sprintf("Environment variable '%s' is not set!", $envKey));
        }

        return $val;
    }
}
