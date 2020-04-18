<?php declare(strict_types=1);

namespace App\WebSocket;

use App\Helper\Atomic;
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
use App\WebSocket\Controller\VideoController;

/**
 * Class VideoModule - This is an module for handle websocket
 *
 * @WsModule(
 *    "video",
 *     messageParser=JsonParser::class,
 *     controllers={VideoController::class}
 *  )
 */
class VideoModule
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
     */
    public function onOpen(Request $request, int $fd)
    {
        /** @var MemoryTable $memoryTable */
        $memoryTable = bean('App\Helper\MemoryTable');
        $memoryTable->store(MemoryTable::SUBJECT_FD_TO_USER, (string)$fd, ['userId' => $request->user]);
        $memoryTable->store(MemoryTable::SUBJECT_USER_TO_FD, (string)$request->user, ['fd' => $fd]);
    }

    /**
     * @OnClose()
     */
    public function onClose(Server $server, int $fd)
    {
        /** @var MemoryTable $memoryTable */
        $memoryTable = bean('App\Helper\MemoryTable');
        $userId = $memoryTable->get(MemoryTable::SUBJECT_FD_TO_USER, (string)$fd, 'userId');
        $selfFd = $memoryTable->get(MemoryTable::SUBJECT_USER_TO_FD, (string)$userId, 'fd');
        $subject = $memoryTable->get(MemoryTable::USER_TO_SUBJECT,(string)$userId,'subject');
        if ($fd == $selfFd) $memoryTable->forget(MemoryTable::SUBJECT_USER_TO_FD, (string)$userId);
        $memoryTable->forget(MemoryTable::SUBJECT_FD_TO_USER, (string)$fd);
        $memoryTable->forget(MemoryTable::USER_TO_SUBJECT, (string)$userId);
        $memoryTable->forget(MemoryTable::SUBJECT_TO_USER,(string)$subject);
    }


}
