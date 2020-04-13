<?php declare(strict_types=1);

namespace App\WebSocket\Controller;

use App\Common\WsMessage;
use App\Helper\MemoryTable;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;

/**
 * Class UserController - This is an controller for handle websocket message request
 *
 * @WsController("user")
 */
class UserController
{

    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @MessageMapping("ping")
     */
    public function index()
    {
        return WEBSOCKET_OPCODE_PONG;
    }

    /**
     * @MessageMapping("getUnreadApplicationCount")
     */
    public function getUnreadApplicationCount()
    {
        $fd = context()->getRequest()->getFd();
        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $userId = $MemoryTable->get(MemoryTable::FD_TO_USER, (string)$fd, 'userId') ?? '';
        $count = $this->userLogic->getUnreadApplicationCount(intval($userId));

        Task::co('User', 'unReadApplicationCount', [$fd, $count]);
    }
}
