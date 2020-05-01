<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Helper\MemoryTable;
use App\Model\Dao\FriendChatHistoryDao;
use App\Model\Dao\FriendGroupDao;
use App\Model\Dao\FriendRelationDao;
use App\Model\Dao\UserApplicationDao;
use App\Model\Dao\UserDao;
use App\Model\Entity\FriendChatHistory;
use App\Model\Entity\FriendGroup;
use App\Model\Entity\FriendRelation;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Task;

/**
 * Class FriendLogic
 * @package App\Model\Logic
 * @Bean()
 */
class FriendLogic
{
    /**
     * @Inject()
     * @var FriendGroupDao
     */
    protected $friendGroupDao;

    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @Inject()
     * @var FriendRelationDao
     */
    protected $friendRelationDao;

    /**
     * @Inject()
     * @var UserDao
     */
    protected $userDao;

    /**
     * @Inject()
     * @var UserApplicationDao
     */
    protected $userApplicationDao;


    /**
     * @Inject()
     * @var FriendChatHistoryDao
     */
    protected $friendChatHistoryDao;

    public function createFriendGroup(int $userId, string $friendGroupName)
    {
        $friendGroupId = $this->friendGroupDao->create(
            [
                'user_id' => $userId,
                'friend_group_name' => $friendGroupName
            ]
        );
        if (!$friendGroupId) throw new \Exception('', ApiCode::FRIEND_GROUP_CREATE_FAIL);

        $result = $this->findFriendGroupById($friendGroupId);

        return $result;
    }

    public function editFriendGroup(int $userId, int $friendGroupId, string $friendGroupName)
    {
        /** @var FriendGroup $friendGroupInfo */
        $friendGroupInfo = $this->findFriendGroupById($friendGroupId);
        if ($friendGroupInfo->getUserId() !== $userId) throw new \Exception(null, ApiCode::NO_PERMISSION_PROCESS);

        return $this->friendGroupDao->editFriendGroupById($friendGroupId,
            [
                'friend_group_name' => $friendGroupName
            ]
        );
    }

    public function delFriendGroup(int $userId, int $friendGroupId)
    {
        /** @var FriendGroup $friendGroupInfo */
        $friendGroupInfo = $this->findFriendGroupById($friendGroupId);
        if ($friendGroupInfo->getUserId() !== $userId) throw new \Exception(null, ApiCode::NO_PERMISSION_PROCESS);

        $friendRelations = $this->getFriendRelationByFriendGroupIds([$friendGroupId]);
        if ($friendRelations->count() > 0) {
            throw new \Exception(null, ApiCode::FRIEND_GROUP_CAN_NOT_DELETE);
        }
        return $this->friendGroupDao->editFriendGroupById($friendGroupId,
            [
                'deleted_at' => date('Y-m-d H:i:s', time())
            ]);
    }

    public function findFriendGroupById(int $friendGroupId)
    {
        $result = $this->friendGroupDao->findFriendGroupById($friendGroupId);
        if (!$result) throw new \Exception('', ApiCode::FRIEND_GROUP_NOT_FOUND);
        return $result;
    }

    public function getFriendGroupByUserId(int $userId)
    {
        return $this->friendGroupDao->getFriendGroupByUserId($userId);
    }

    public function getFriend()
    {
        $request = context()->getRequest();

        $friendGroups = $this->getFriendGroupByUserId($request->user);
        $friendGroupIds = array_column($friendGroups->toArray(), 'friendGroupId');


        $friendRelations = $this->getFriendRelationByFriendGroupIds($friendGroupIds);
        $friendRelationIds = array_column($friendRelations->toArray(), 'friendId');

        $users = $this->userDao->getUserByIds($friendRelationIds)->toArray();
        $userInfos = array_column($users, null, 'userId');

        $friend = [];

        /** @var FriendGroup $friendGroup */
        foreach ($friendGroups as $friendGroup) {
            $friend[$friendGroup->getFriendGroupId()] = [
                'id' => $friendGroup->getFriendGroupId(),
                'groupname' => $friendGroup->getFriendGroupName(),
                'list' => []
            ];
        }

        /** @var FriendRelation $friendRelation */
        foreach ($friendRelations as $friendRelation) {
            $userInfo = $userInfos[$friendRelation->getFriendId()];
            $friend[$friendRelation->getFriendGroupId()]['list'][] = [
                'username' => $userInfo['username'],
                'id' => $userInfo['userId'],
                'avatar' => $userInfo['avatar'],
                'sign' => $userInfo['sign'],
                'status' => FriendRelation::STATUS_TEXT[$userInfo['status']],
            ];
        }
        return array_values($friend);
    }

    public function getFriendRelationByFriendGroupIds(array $friendGroupIds)
    {
        return $this->friendRelationDao->getFriendRelationByFriendGroupIds($friendGroupIds);
    }

    public function getRecommendedFriend(int $limit)
    {
        return $this->userDao->getRecommendedFriend($limit);
    }

    public function searchFriend(string $keyword, int $page, int $size)
    {
        return $this->userDao->searchFriend($keyword, $page, $size);
    }

    public function apply(int $userId, int $receiverId, int $friendGroupId, string $applicationReason)
    {
        if ($userId == $receiverId) throw new \Exception('', ApiCode::FRIEND_NOT_ADD_SELF);

        /** @var FriendRelation $check */
        $check = $this->friendRelationDao->checkIsFriendRelation($userId, $receiverId);
        if ($check) throw new \Exception('', ApiCode::FRIEND_RELATION_ALREADY);

        $this->userDao->findUserInfoById($userId);
        ($receiverId);

        $friendGroupInfo = $this->friendGroupDao->findFriendGroupById($friendGroupId);
        if (!$friendGroupInfo) throw new \Exception('', ApiCode::FRIEND_GROUP_NOT_FOUND);

        $result = $this->userLogic->createUserApplication($userId, $receiverId, $friendGroupId, UserApplication::APPLICATION_TYPE_FRIEND, $applicationReason, UserApplication::APPLICATION_STATUS_CREATE, UserApplication::UN_READ);
        if (!$result) throw new \Exception('', ApiCode::USER_CREATE_APPLICATION_FAIL);

        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$receiverId, 'fd') ?? '';
        if ($fd) {
            Task::co('User', 'unReadApplicationCount', [$fd, '新']);
        }

        return $result;
    }

    public function agreeApply(int $userApplicationId, int $friendGroupId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_FRIEND);

        $this->findFriendGroupById($userApplicationInfo->getGroupId());
        $this->findFriendGroupById($friendGroupId);

        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_ACCEPT);


        /** @var FriendRelation $check */
        $fromCheck = $this->friendRelationDao->checkIsFriendRelation($userApplicationInfo->getReceiverId(), $userApplicationInfo->getUserId());
        $toCheck = $this->friendRelationDao->checkIsFriendRelation($userApplicationInfo->getUserId(), $userApplicationInfo->getReceiverId());


        if (!$fromCheck) {
            $this->createFriendRelation(
                $userApplicationInfo->getReceiverId()
                , $userApplicationInfo->getUserId()
                , $friendGroupId
            );

            $this->createFriendRelation(
                $userApplicationInfo->getUserId()
                , $userApplicationInfo->getReceiverId()
                , $userApplicationInfo->getGroupId()
            );
        }

        if ($fromCheck && $toCheck) throw new \Exception('', ApiCode::FRIEND_RELATION_ALREADY);

        /** @var User $friendInfo */
        $friendInfo = $this->userLogic->findUserInfoById($userApplicationInfo->getUserId());

        /** @var User $selfInfo */
        $selfInfo = $this->userLogic->findUserInfoById($userApplicationInfo->getReceiverId());

        $pushUserInfo = [
            'type' => UserApplication::APPLICATION_TYPE_FRIEND,
            'avatar' => $selfInfo->getAvatar(),
            'username' => $selfInfo->getUsername(),
            'groupid' => $userApplicationInfo->getGroupId(),
            'id' => $selfInfo->getUserId(),
            'sign' => $selfInfo->getSign(),
            'status' => $selfInfo->getStatus()
        ];

        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$friendInfo->getUserId(), 'fd') ?? '';
        if ($fd) {
            Task::co('Friend', 'agreeApply', [$fd, $pushUserInfo]);
            Task::co('User', 'unReadApplicationCount', [$fd, '新']);

        }

        return [
            'type' => UserApplication::APPLICATION_TYPE_FRIEND,
            'avatar' => $friendInfo->getAvatar(),
            'username' => $friendInfo->getUsername(),
            'id' => $friendInfo->getUserId(),
            'sign' => $friendInfo->getSign(),
            'groupid' => $friendGroupId,
            'status' => FriendRelation::STATUS_TEXT[$friendInfo->getStatus()]
        ];
    }

    public function refuseApply(int $userApplicationId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_FRIEND);
        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_REFUSE);

        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');
        $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, (string)$userApplicationInfo->getUserId(), 'fd') ?? '';
        if ($fd) {
            Task::co('User', 'unReadApplicationCount', [$fd, '新']);
        }
        return $userApplicationInfo;
    }

    public function createFriendRelation(int $userId, int $friendId, int $groupId)
    {
        return $this->friendRelationDao->createFriendRelation([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'friend_group_id' => $groupId,
        ]);
    }


    public function createFriendChatHistory(
        string $messageId,
        int $fromUserId,
        int $toUserId,
        string $content,
        int $receptionState = FriendChatHistory::NOT_RECEIVED)
    {
        $data = [
            'message_id' => $messageId,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'content' => $content,
            'reception_state' => $receptionState
        ];
        $id = $this->friendChatHistoryDao->createFriendChatHistory($data);
        return $this->friendChatHistoryDao->findFriendChatHistoryById($id);
    }

    public function setFriendChatHistoryReceptionStateByMessageId(string $messageId, int $receptionState = FriendChatHistory::RECEIVED)
    {
        return $this->friendChatHistoryDao->setFriendChatHistoryReceptionStateByMessageId($messageId, $receptionState);
    }


    public function getUnreadMessageByToUserId(int $userId)
    {

        /** @var FriendChatHistory $historyInfos */
        $historyInfos = $this->friendChatHistoryDao->getUnreadMessageByToUserId($userId);

        $userIds = [$userId];
        /** @var FriendChatHistory $historyInfo */
        foreach ($historyInfos as $historyInfo) {
            array_push($userIds, $historyInfo->getFromUserId());
        }


        /** @var User $userInfos */
        $userInfos = array_column($this->userDao->getUserByIds($userIds)->toArray(), null, 'userId');

        $result = [];


        /** @var FriendChatHistory $historyInfo */
        foreach ($historyInfos as $historyInfo) {
            $fromUserId = $historyInfo->getFromUserId();
            $result[] = [
                'username' => $userInfos[$fromUserId]['username'],
                'avatar' => $userInfos[$fromUserId]['avatar'],
                'from_user_id' => $fromUserId,
                'content' => $historyInfo->getContent(),
                'message_id' => $historyInfo->getMessageId(),
                'timestamp' => strtotime($historyInfo->getCreatedAt()) * 1000
            ];
        }


        return $result;
    }


    public function getChatHistory(int $fromUserId, int $userId, int $page, int $size)
    {
        /** @var FriendChatHistory $historyInfos */
        $historyInfos = $this->friendChatHistoryDao->getChatHistory($fromUserId, $userId, $page, $size);

        /** @var User $userInfos */
        $userInfos = array_column($this->userDao->getUserByIds([$fromUserId, $userId])->toArray(), null, 'userId');

        $result = [
            'count' => $historyInfos['count'],
            'page' => $historyInfos['page'],
            'perPage' => $historyInfos['perPage'],
            'pageCount' => $historyInfos['pageCount'],
        ];
        foreach ($historyInfos['list'] as $historyInfo) {
            $id = $historyInfo['fromUserId'];
            $result['list'][] = [
                'id' => $id,
                'username' => $userInfos[$id]['username'],
                'avatar' => $userInfos[$id]['avatar'],
                'content' => $historyInfo['content'],
                'timestamp' => strtotime($historyInfo['createdAt']) * 1000
            ];
        }

        return $result;

    }


}
