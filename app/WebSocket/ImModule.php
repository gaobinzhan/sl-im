<?php declare(strict_types=1);

namespace App\WebSocket;

use App\Helper\JwtHelper;
use App\Helper\MemoryTable;
use App\Model\Dao\UserDao;
use App\Model\Entity\User;
use App\Model\Logic\UserLogic;
use App\WebSocket\Controller\UserController;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\OnMessage;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandshake;
use Swoft\WebSocket\Server\MessageParser\JsonParser;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\WebSocket\Controller\FriendController;
use App\WebSocket\Controller\GroupController;

/**
 * Class ImModule - This is an module for handle websocket
 *
 * @WsModule(
 *    "im",
 *     messageParser=JsonParser::class,
 *     controllers={FriendController::class,UserController::class,GroupController::class}
 *  )
 */
class ImModule
{

    /**
     * @OnHandshake()
     */
    public function onHandshake(Request $request, Response $response)
    {
        $token = $request->getHeaderLine('sec-websocket-protocol');
        $userId = JwtHelper::decrypt($token);
        if (!$userId) return [false, $response];

        /** @var UserDao $userDao */
        $userDao = bean('App\Model\Dao\UserDao');
        /** @var User $userInfo */
        $userInfo = $userDao->findUserInfoById($userId);
        if (!$userInfo) return [false, $response];

        $request->user = $userInfo->getUserId();
        $request->userInfo = $userInfo;

        return [true, $response];
    }

    /**
     * @OnOpen()
     * @param Request $request
     * @param int $fd
     * @return mixed
     */
    public function onOpen(Request $request, int $fd)
    {
        /** @var MemoryTable $memoryTable */
        $memoryTable = bean('App\Helper\MemoryTable');

        $checkOnline = $memoryTable->get(MemoryTable::USER_TO_FD, (string)$request->user, 'fd');
        if ($checkOnline) {
            \server()->disconnect($checkOnline, 0, '你的帐号在别的地方登录！');
        }

        $memoryTable->store(MemoryTable::FD_TO_USER, (string)$fd, ['userId' => $request->user]);
        $memoryTable->store(MemoryTable::USER_TO_FD, (string)$request->user, ['fd' => $fd]);
        /** @var UserLogic $userLogic */
        $userLogic = bean('App\Model\Logic\UserLogic');
        $userLogic->setUserStatus($request->user, User::STATUS_ONLINE);

    }

    /**
     * @OnClose()
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onClose(Server $server, int $fd)
    {
        /** @var MemoryTable $memoryTable */
        $memoryTable = bean('App\Helper\MemoryTable');
        $userId = $memoryTable->get(MemoryTable::FD_TO_USER, (string)$fd, 'userId');
        $selfFd = $memoryTable->get(MemoryTable::FD_TO_USER, (string)$userId, 'fd');
        if ($fd == $selfFd) $memoryTable->forget(MemoryTable::USER_TO_FD, (string)$userId);
        $memoryTable->forget(MemoryTable::FD_TO_USER, (string)$fd);
        /** @var UserLogic $userLogic */
        $userLogic = bean('App\Model\Logic\UserLogic');
        $userLogic->setUserStatus($userId, User::STATUS_OFFLINE);
    }
}
