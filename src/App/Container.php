<?php

declare(strict_types=1);

namespace App;

use Fearandgreed\PageClient;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package App
 */
class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private array $elements = [];

    /**
     * @var \Monolog\Logger
     */
    private ?\Monolog\Logger $logger = null;

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->elements[$key];
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return isset($this->elements[$key]);
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $this->elements[$key] = $value;

        return $this;
    }

    /**
     * @return PageClient
     */
    public function getPageClient(): PageClient
    {
        return new PageClient($this);
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger(): \Monolog\Logger
    {
        if (null === $this->logger) {
            $this->logger = new \Monolog\Logger('FearAndGreed');
            $this->logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $this->logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));
        }

        return $this->logger;
    }
}