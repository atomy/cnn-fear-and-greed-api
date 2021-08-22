<?php

use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Error\Renderers\PlainTextErrorRenderer;
use App\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Error\Renderers\JsonErrorRenderer;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

if (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === \App\EnvHelper::PHPUNIT_USER_AGENT) {
    putenv('CNN_SITE=http://cnn-fear-and-greed-phiremock');
}

// Instantiate App
AppFactory::setContainer(new Container());
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addMiddleware(new \App\JsonResponseMiddleware);

/** @var Container $c */
$c = $app->getContainer();
$c->set(PlainTextErrorRenderer::class, new JsonErrorRenderer());
$c->set(HtmlErrorRenderer::class, new JsonErrorRenderer());

$app->get('/index', function (Request $request, Response $response, $args) use ($app) {
    return (new \Fearandgreed\Controller\IndexController($app->getContainer()))->get($request, $response, []);
});
$app->get('/health', function (Request $request, Response $response, $args) use ($app) {
    return (new \Fearandgreed\Controller\HealthController($app->getContainer()))->health($request, $response, []);
});

// Run app
$app->run();
