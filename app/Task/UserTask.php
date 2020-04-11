<?php declare(strict_types=1);

namespace App\Task;

use App\Common\WsMessage;
use App\Model\Dao\UserDao;
use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;

/**
 * Class UserTask - define some tasks
 *
 * @Task("User")
 * @package App\Task
 */
class UserTask
{
    /**
     * @TaskMapping(name="setUserStatus")
     */
    public function setUserStatus(array $fds,array $data)
    {
        if (empty($fds)) return false;
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT,WsMessage::EVENT_USER_STATUS,$data);
        server()->broadcast($result,$fds);
    }
}
