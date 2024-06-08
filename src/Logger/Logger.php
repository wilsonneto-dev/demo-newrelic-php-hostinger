<?php

namespace App\Logger;

use Monolog\Handler\FirePHPHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function getLogger($name = 'app'): MonologLogger
    {
        $logger = new MonologLogger($name);

        $logFile = __DIR__ . '/../../logs/app.log';
        $streamHandler = new StreamHandler($logFile, MonologLogger::DEBUG);
        $logger->pushHandler($streamHandler);
        $logger->pushHandler(new FirePHPHandler());

        return $logger;
    }
}