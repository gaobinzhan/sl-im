<?php declare(strict_types=1);


namespace App\WebSocket\Controller;


use App\ExceptionCode\ApiCode;
use App\Helper\MemoryTable;
use App\Model\Entity\User;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Task;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class VideoController
 * @package App\WebSocket\Controller
 * @WsController("video")
 */
class VideoController
{
    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @MessageMapping("friendBusy")
     */
    public function friendBusy(Message $message)
    {
        $fd = context()->getRequest()->getFd();
        $data = $message->getData();

        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');

        $selfUserID = $MemoryTable->get(MemoryTable::FD_TO_USER, (string)$fd, 'userId') ?? '';
        $selfSubject = $MemoryTable->get(MemoryTable::USER_TO_SUBJECT, (string)$selfUserID, 'subject');

        if ($selfSubject) throw new \Exception('', ApiCode::USER_IN_VIDEO_CALL);

        $toFd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$data['to_user_id'], 'fd') ?? '';
        $toSubject = $MemoryTable->get(MemoryTable::USER_TO_SUBJECT, (string)$data['to_user_id'], 'subject');
        if ($toSubject) throw new \Exception('', ApiCode::FRIEND_CALL_IN_PROGRESS);

        /** @var User $selfUserInfo */
        $selfUserInfo = $this->userLogic->findUserInfoById($selfUserID);
        /** @var User $toUserInfo */
        $toUserInfo = $this->userLogic->findUserInfoById($data['to_user_id']);

        Task::co('Video', 'createFriendVideo', [
            (int)$selfUserID,
            (int)$fd,
            (int)$toFd,
            (string)$selfUserInfo->getUsername(),
            (string)$toUserInfo->getUsername()]);
    }


    /**
     * @MessageMapping("friendSubscribe")
     */
    public function friendSubscribe(Message $message)
    {
        $data = $message->getData();
        $request = context()->getRequest();
        $fd = $request->getFd();
        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');

        $subject = $data['subject'];
        $userId = $MemoryTable->get(MemoryTable::SUBJECT_FD_TO_USER, (string)$fd, 'userId');
        $userIds = $MemoryTable->get(MemoryTable::SUBJECT_TO_USER, (string)$subject, 'userId') ?? [];
        if ($userIds) {
            $userIds = explode(',', (string)$userIds);
            array_push($userIds, $userId);
        } else {
            $userIds = [$userId];
        }
        $MemoryTable->store(MemoryTable::USER_TO_SUBJECT, (string)$userId, ['subject' => $subject]);
        $MemoryTable->store(MemoryTable::SUBJECT_TO_USER, (string)$subject, ['userId' => implode(',', $userIds)]);

        if (count($userIds) == 2) {
            foreach ($userIds as $userId){
                $toFd = $MemoryTable->get(MemoryTable::SUBJECT_USER_TO_FD, (string)$userId, 'fd');
                server()->sendTo((int)$toFd,
                    json_encode([
                            'event' => 'accept',
                        ]
                    )
                );
            }
        }
    }

    /**
     * @MessageMapping("friendPublish")
     */
    public function friendPublish(Message $message)
    {
        $data = $message->getData();
        $request = context()->getRequest();
        $fd = $request->getFd();
        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $selfUserId = $MemoryTable->get(MemoryTable::SUBJECT_FD_TO_USER, (string)$fd, 'userId');
        $subject = $data['subject'];
        $event = $data['event'];
        $data = $data['data'];
        $userIds = $MemoryTable->get(MemoryTable::SUBJECT_TO_USER, (string)$subject, 'userId') ?? [];
        $userIds = explode(',', $userIds);
        foreach ($userIds as $userId) {
            if ($selfUserId == $userId) continue;
            $toFd = $MemoryTable->get(MemoryTable::SUBJECT_USER_TO_FD, (string)$userId, 'fd');
            server()->sendTo((int)$toFd,
                json_encode([
                        'event' => $event,
                        'data' => $data
                    ]
                )
            );
        }
    }
}
