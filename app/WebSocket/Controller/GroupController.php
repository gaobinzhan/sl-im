<?php declare(strict_types=1);

namespace App\WebSocket\Controller;

use App\ExceptionCode\ApiCode;
use App\Helper\MemoryTable;
use App\Model\Entity\GroupChatHistory;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use App\Model\Logic\GroupLogic;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class GroupController - This is an controller for handle websocket message request
 *
 * @WsController("group")
 */
class GroupController
{

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
     * @MessageMapping("send")
     */
    public function sendMessage(Message $message)
    {
        $data = $message->getData();

        $check = $this->groupLogic->checkNotGroupRelation($data['from_user_id'], $data['to_id']);
        if (!$check) throw new \Exception($data['message_id'], ApiCode::GROUP_NOT_MEMBER);

        /** @var GroupChatHistory $groupChatHistoryInfo */
        $groupChatHistoryInfo = $this->groupLogic->createGroupChatHistory($data['message_id'], $data['from_user_id'], $data['to_id'], $data['content']);

        /** @var User $userInfo */
        $userInfo = $this->userLogic->findUserInfoById($data['from_user_id']);

        $userIds = $this->groupLogic->getGroupRelationUserIdsById($data['to_id'])->toArray();
        $userIds = array_column($userIds, 'userId');


        $fds = [];

        $selfFd = context()->getRequest()->getFd();


        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        foreach ($userIds as $userId) {
            $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$userId, 'fd') ?? '';
            if ($fd && ($fd != $selfFd)) array_push($fds, $fd);
        }

        Task::co('Group', 'sendMessage', [
            $fds,
            $userInfo->getUsername(),
            $userInfo->getAvatar(),
            $data['to_id'],
            UserApplication::APPLICATION_TYPE_GROUP,
            $data['content'],
            $data['message_id'],
            false,
            $data['from_user_id'],
            strtotime($groupChatHistoryInfo->getCreatedAt()) * 1000
        ]);

        return ['message_id' => $data['message_id'] ?? ''];

    }
}
