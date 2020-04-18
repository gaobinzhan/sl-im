<?php declare(strict_types=1);

namespace App\Task\Task;

use App\Common\WsMessage;
use App\Helper\Atomic;
use App\Helper\MemoryTable;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoole\Table;

/**
 * Class VideoTask - define some tasks
 *
 * @Task("Video")
 * @package App\Task\Task
 */
class VideoTask
{
    /**
     * @TaskMapping(name="createFriendVideo")
     */
    public function createFriendVideo(int $userId, ?int $fromFd, ?int $toFd, string $formUserName, string $toUserName)
    {
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_FRIEND_VIDEO_ROOM, [
            'roomId' => md5((string)$userId),
            'userId' => $userId,
            'fromUserName' => $formUserName,
            'toUserName' => $toUserName
        ]);

        server()->sendTo((int)$fromFd, $result);
        server()->sendTo((int)$toFd, $result);
    }
}
