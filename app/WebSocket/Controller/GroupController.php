<?php declare(strict_types=1);

namespace App\WebSocket\Controller;

use App\Helper\MemoryTable;
use App\Model\Entity\FriendChatHistory;
use App\Model\Entity\GroupChatHistory;
use App\Model\Logic\GroupLogic;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class GroupController - This is an controller for handle websocket message request
 *
 * @WsController("group")
 */
class GroupController{

    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @Inject()
     * @var GroupLogic
     */
    protected $groupLogic;

    /**
     * @TaskMapping(name="send")
     */
    public function sendMessage(Message $message)
    {
        $data = $message->getData();

        /** @var GroupChatHistory $groupChatHistoryInfo */
        $groupChatHistoryInfo = $this->groupLogic->createGroupChatHistory($data['message_id'], $data['from_user_id'], $data['to_id'], $data['content']);

        $userInfo = $this->userLogic->findUserInfoById($data['from_user_id']);

        $userIds = $this->groupLogic->getGroupRelationUserIdsById($data['to_id']);

        var_dump($userIds);

        $fds = [];

        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        foreach ($userIds as $userId) {
            $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$data['to_id'], 'fd') ?? '';
            array_push($fds,$fd);
        }

    }
}
