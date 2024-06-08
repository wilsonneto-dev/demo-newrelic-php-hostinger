<?php

namespace App\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Twig\Environment;

class CustomErrorHandler extends ErrorHandler
{
    protected LoggerInterface $logger;
    protected Environment $twig;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
        Environment $twig
    ) {
        parent::__construct($callableResolver, $responseFactory);
        $this->logger = $logger;
        $this->twig = $twig;
    }

    protected function respond(): ResponseInterface
    {
        // Log the error
        $exception = $this->exception;
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);

        // Determine the status code
        $statusCode = 500;
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        // Render the error page
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($this->twig->render('error.twig', [
            'message' => $exception->getMessage(),
        ]));

        return $response;
    }
}
