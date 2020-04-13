<?php declare(strict_types=1);

namespace App\Task\Task;

use App\Common\WsMessage;
use App\Helper\Atomic;
use App\Helper\MemoryTable;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoole\Table;

/**
 * Class UserTask - define some tasks
 *
 * @Task("User")
 * @package App\Task\Task
 */
class UserTask
{
    /**
     * @TaskMapping(name="setUserStatus")
     */
    public function setUserStatus(array $fds, array $data)
    {
        if (empty($fds)) return false;
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_USER_STATUS, $data);
        server()->broadcast($result, $fds);
    }

    /**
     * @TaskMapping(name="onlineNumber")
     */
    public function onlineNumber()
    {
        /** @var Atomic $atomic */
        $atomic = Bean('App\Helper\Atomic');

        /** @var MemoryTable $memoryTable */
        $memoryTable = bean('App\Helper\MemoryTable');

        /** @var Table $userToFdTable */
        $userToFdTable = $memoryTable->getTable(MemoryTable::USER_TO_FD);

        $fds = [];
        foreach ($userToFdTable as $item) {
            array_push($fds, $item['fd']);
        }

        $data = wsSuccess(
            WsMessage::WS_MESSAGE_CMD_EVENT,
            'onlineNumber',
            "<span>当前在线人数：<b>{$atomic->get()}</b></span>"
        );

        server()->broadcast($data, $fds);
    }

    /**
     * @TaskMapping(name="unReadApplicationCount")
     */
    public function unReadApplicationCount(int $fd, $data)
    {
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_UNREAD_APPLICATION_COUNT, $data);
        server()->sendTo($fd, $result);
    }
}
