<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

use Swoft\Http\Server\HttpServer;
use Swoft\Task\Swoole\TaskListener;
use Swoft\Task\Swoole\FinishListener;
use Swoft\Rpc\Client\Client as ServiceClient;
use Swoft\Rpc\Client\Pool as ServicePool;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Http\Server\Swoole\RequestListener;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoft\Server\SwooleEvent;
use Swoft\Db\Database;

return [
    'noticeHandler' => [
        'logFile' => '@runtime/logs/notice-%d{Y-m-d-H}.log',
    ],
    'applicationHandler' => [
        'logFile' => '@runtime/logs/error-%d{Y-m-d}.log',
    ],
    'logger' => [
        'flushRequest' => true,
        'enable' => true,
        'json' => false,
    ],
    'httpServer' => [
        'class' => HttpServer::class,
        'port' => 9091,
        'pidName' => 'IM-http',
        'listener' => [
        ],
        'process' => [
        ],
        'on' => [
            SwooleEvent::TASK => bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => bean(FinishListener::class)
        ],
        /* @see HttpServer::$setting */
        'setting' => [
            'task_worker_num' => 12,
            'task_enable_coroutine' => true,
            'worker_num' => 6,
            // enable static handle
            'enable_static_handler' => true,
            // swoole v4.4.0以下版本, 此处必须为绝对路径
            'document_root' => dirname(__DIR__) . '/public',
        ]
    ],
    'httpDispatcher' => [
        // Add global http middleware
        'middlewares' => [
//            \App\Http\Middleware\FavIconMiddleware::class,
            \Swoft\Http\Session\SessionMiddleware::class,
            // \Swoft\Whoops\WhoopsMiddleware::class,
            // Allow use @View tag
            \Swoft\View\Middleware\ViewMiddleware::class,
        ],
        'afterMiddlewares' => [
            \Swoft\Http\Server\Middleware\ValidatorMiddleware::class
        ]
    ],
    'sessionManager' => [
        'class' => \Swoft\Http\Session\SessionManager::class,
        'name' => 'IM_SESSION_ID'
    ],
    'db' => [
        'class' => Database::class,
        'dsn' => 'mysql:dbname=im;host=127.0.0.1:3306',
        'username' => 'root',
        'password' => 'gaobinzhan',
        'charset' => 'utf8mb4',
    ],
    'db.pool' => [
        'class' => \Swoft\Db\Pool::class,
        'database' => bean('db'),
        'minActive' => 5,
        'maxActive' => 10
    ],
    'migrationManager' => [
        'migrationPath' => '@database/Migration',
    ],
    'wsServer' => [
        'class' => WebSocketServer::class,
        'pidName' => 'IM-ws',
        'port' => 9091,
        'listener' => [
        ],
        'on' => [
            // Enable http handle
            SwooleEvent::REQUEST => bean(RequestListener::class),
            // Enable task must add task and finish event
            SwooleEvent::TASK => bean(TaskListener::class),
            SwooleEvent::FINISH => bean(FinishListener::class)
        ],
        'debug' => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting' => [
            'task_worker_num' => 12,
            'task_enable_coroutine' => true,
            'worker_num' => 6,
            // enable static handle
            'enable_static_handler' => env('ENABLE_STATIC_HANDLER', true),
            // swoole v4.4.0以下版本, 此处必须为绝对路径
            'document_root' => env('DOCUMENT_ROOT', dirname(__DIR__) . '/public'),
            'log_file' => alias('@runtime/swoole.log'),
        ],
    ],
];
