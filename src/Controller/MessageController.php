<?php

namespace App\Controller;

use App\Gateway\MessagesGateway;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class MessageController
{
    private $messagesGateway;
    private $twig;
    private $logger;

    public function __construct(MessagesGateway $messagesGateway, Environment $twig, LoggerInterface $logger)
    {
        $this->messagesGateway = $messagesGateway;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function showLogin(Request $request, Response $response): Response
    {
        $this->logger->info('Rendering login page');
        $response->getBody()->write($this->twig->render('login.twig'));
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        if (isset($data['username'])) {
            $_SESSION['username'] = $data['username'];
            $this->logger->info('User logged in', ['username' => $data['username']]);
            return $response->withHeader('Location', '/feed')->withStatus(302);
        }

        $this->logger->warning('Failed login attempt');
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function showFeed(Request $request, Response $response): Response
    {
        $this->logger->info('Rendering feed page');

        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $messages = $this->messagesGateway->getMessagesWithPagination($limit, $offset);
        $totalMessages = $this->messagesGateway->getTotalMessageCount();
        $totalPages = ceil($totalMessages / $limit);

        $response->getBody()->write($this->twig->render('timeline.twig', [
            'messages' => $messages,
            'username' => $_SESSION['username'],
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]));
        return $response;
    }

    public function postMessage(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        if (isset($data['message']) && isset($_SESSION['username'])) {
            $this->messagesGateway->addMessage($_SESSION['username'], $data['message']);
            $this->logger->info('Message posted', ['username' => $_SESSION['username'], 'message' => $data['message']]);
        }

        return $response->withHeader('Location', '/feed')->withStatus(302);
    }

    public function deleteMessage(Request $request, Response $response, $args): Response
    {
        $messageId = $args['id'];
        $username = $_SESSION['username'];

        $message = $this->messagesGateway->getMessageById($messageId);

        if ($message && $message['username'] === $username) {
            $this->messagesGateway->deleteMessage($messageId);
            $this->logger->info('Message deleted', ['username' => $username, 'message_id' => $messageId]);
            $_SESSION['flash_success'] = 'Message deleted';
        } else {
            $this->logger->warning('Unauthorized delete attempt', ['username' => $username, 'message_id' => $messageId]);
            $_SESSION['flash_error'] = 'Failed to delete message';
        }

        return $response->withHeader('Location', '/feed')->withStatus(302);
    }
}
