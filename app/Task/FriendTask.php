<?php declare(strict_types=1);

namespace App\Task;


use App\Common\WsMessage;
use App\Helper\MemoryTable;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;

/**
 * Class FriendTask - define some tasks
 *
 * @Task("Friend")
 * @package App\Task
 */
class FriendTask
{

    /**
     * @TaskMapping(name="sendMessage")
     */
    public function sendMessage(
        $fd,
        $username,
        $avatar,
        $userId,
        $type,
        $content,
        $cid,
        $mine,
        $fromId,
        $timestamp
    )
    {
        if (!$fd) return false;
        $data = [
            'username' => $username,
            'avatar' => $avatar,
            'id' => $userId,
            'type' => $type,
            'content' => $content,
            'cid' => $cid,
            'mine' => $mine,
            'fromid' => $fromId,
            'timestamp' => $timestamp,
        ];
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_MESSAGE, $data);

        server()->sendTo($fd, $result);
    }
}
