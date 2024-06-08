<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class FlashMessageMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    private $twig;
    private $logger;

    public function __construct(Environment $twig, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $flashSuccess = null;
        $flashError = null;

        if (isset($_SESSION['flash_success'])) {
            $flashSuccess = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        }

        if (isset($_SESSION['flash_error'])) {
            $flashError = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
        }

        // Add flash messages to Twig global context
        if($flashSuccess || $flashError) {
            $this->twig->addGlobal('flash_success', $flashSuccess);
            $this->twig->addGlobal('flash_error', $flashError);
            $this->logger->info('Flash messages added to Twig context', ['flash_success' => $flashSuccess, 'flash_error' => $flashError]);
        }

        return $handler->handle($request);
    }
}
