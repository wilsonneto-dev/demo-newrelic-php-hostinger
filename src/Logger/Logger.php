<?php

namespace App\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function getLogger($name = 'app')
    {
        $logger = new MonologLogger($name);

        $logFile = __DIR__ . '/../../logs/app.log';
        $streamHandler = new StreamHandler($logFile, MonologLogger::DEBUG);
        $logger->pushHandler($streamHandler);

        return $logger;
    }
}