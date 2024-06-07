<?php

require_once '../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Dotenv\Dotenv;
use App\Controller\MessageController;
use App\Middleware\SessionMiddleware;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Logger\Logger;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();

echo sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST'),
    getenv('DB_NAME'));

$container->set('pdo', function () {
    return new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST'),
            getenv('DB_NAME')),
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
});

$container->set('twig', function () {
    $loader = new FilesystemLoader(__DIR__ . '/../src/Views');
    return new Environment($loader);
});

$container->set('messagesGateway', function ($c) {
    return new \App\Gateway\MessagesGateway($c->get('pdo'));
});

$container->set('logger', function () {
    return Logger::getLogger();
});

$container->set('MessageController', function ($c) {
    return new MessageController($c->get('messagesGateway'), $c->get('twig'), $c->get('logger'));
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new SessionMiddleware($container->get('logger')));

$app->get('/', 'MessageController:showLogin');
$app->post('/login', 'MessageController:login');
$app->get('/feed', 'MessageController:showFeed');
$app->post('/feed', 'MessageController:postMessage');

$app->run();
