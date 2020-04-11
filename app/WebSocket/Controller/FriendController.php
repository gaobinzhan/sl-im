<?php declare(strict_types=1);

namespace App\WebSocket\Controller;

use App\Helper\MemoryTable;
use App\Model\Entity\FriendChatHistory;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use App\Model\Logic\FriendLogic;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class FriendController - This is an controller for handle websocket message request
 *
 * @WsController("friend")
 */
class FriendController
{
    /**
     * @Inject()
     * @var FriendLogic
     */
    protected $friendLogic;

    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @MessageMapping("send")
     */
    public function sendMessage(Message $message)
    {
        $data = $message->getData();

        /** @var FriendChatHistory $friendChatHistoryInfo */
        $friendChatHistoryInfo = $this->friendLogic->createFriendChatHistory($data['message_id'], $data['from_user_id'], $data['to_user_id'], $data['content']);

        $userInfo = $this->userLogic->findUserInfoById($data['from_user_id']);
        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$data['to_user_id'], 'fd') ?? '';
        Task::co('Friend', 'sendMessage', [
            $fd,
            $userInfo->getUsername(),
            $userInfo->getAvatar(),
            $data['from_user_id'],
            UserApplication::APPLICATION_TYPE_FRIEND,
            $data['content'],
            $data['message_id'],
            false,
            $data['from_user_id'],
            strtotime($friendChatHistoryInfo->getCreatedAt()) * 1000
        ]);

        return ['message_id' => $data['message_id'] ?? ''];
    }

    /**
     * @MessageMapping("read")
     */
    public function readMessage(Message $message)
    {
        $this->friendLogic->setFriendChatHistoryReceptionStateByMessageId(
            $message->getData()['message_id'],
            FriendChatHistory::RECEIVED
        );

        return ['message_id' => $message->getData()['message_id'] ?? ''];
    }

}
