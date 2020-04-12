<?php declare(strict_types=1);

namespace App\Task;


use App\Common\WsMessage;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;

/**
 * Class GroupTask - define some tasks
 *
 * @Task("Group")
 * @package App\Task
 */
class GroupTask
{
    /**
     * @TaskMapping(name="sendMessage")
     */
    public function sendMessage(
        $fds,
        $username,
        $avatar,
        $groupId,
        $type,
        $content,
        $cid,
        $mine,
        $fromId,
        $timestamp
    )
    {
        if (!$fds) return false;
        $data = [
            'username' => $username,
            'avatar' => $avatar,
            'id' => $groupId,
            'type' => $type,
            'content' => $content,
            'cid' => $cid,
            'mine' => $mine,
            'fromid' => $fromId,
            'timestamp' => $timestamp,
        ];
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_MESSAGE, $data);

        server()->broadcast($result, $fds);
    }
}
