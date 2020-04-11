<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Model\Dao\FriendGroupDao;
use App\Model\Dao\FriendRelationDao;
use App\Model\Dao\UserApplicationDao;
use App\Model\Dao\UserDao;
use App\Model\Entity\FriendGroup;
use App\Model\Entity\FriendRelation;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

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

    public function apply(int $userId, int $receiverId, int $groupId, string $applicationReason)
    {
        if ($userId == $receiverId) throw new \Exception('', ApiCode::FRIEND_NOT_ADD_SELF);

        /** @var FriendRelation $check */
        $check = $this->friendRelationDao->checkIsFriendRelation($userId, $receiverId);
        if ($check) throw new \Exception('', ApiCode::FRIEND_RELATION_ALREADY);

        $this->userLogic->findUserInfoById($receiverId);

        $friendGroupInfo = $this->friendGroupDao->findFriendGroupById($groupId);
        if (!$friendGroupInfo) throw new \Exception('', ApiCode::FRIEND_GROUP_NOT_FOUND);

        $result = $this->userLogic->createUserApplication($userId, $receiverId, $groupId, UserApplication::APPLICATION_TYPE_FRIEND, $applicationReason, UserApplication::APPLICATION_STATUS_CREATE, UserApplication::UN_READ);
        if (!$result) throw new \Exception('', ApiCode::USER_CREATE_APPLICATION_FAIL);

        return $result;
    }

    public function agreeApply(int $userApplicationId, int $groupId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_FRIEND);

        $this->findFriendGroupById($userApplicationInfo->getGroupId());
        $this->findFriendGroupById($groupId);

        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_ACCEPT);


        /** @var FriendRelation $check */
        $fromCheck = $this->friendRelationDao->checkIsFriendRelation($userApplicationInfo->getReceiverId(), $userApplicationInfo->getUserId());
        $toCheck = $this->friendRelationDao->checkIsFriendRelation($userApplicationInfo->getUserId(), $userApplicationInfo->getReceiverId());


        if (!$fromCheck) {
            $this->createFriendRelation(
                $userApplicationInfo->getReceiverId()
                , $userApplicationInfo->getUserId()
                , $groupId
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

        return [
            'type' => UserApplication::APPLICATION_TYPE_FRIEND,
            'avatar' => $friendInfo->getAvatar(),
            'username' => $friendInfo->getUsername(),
            'id' => $friendInfo->getUserId(),
            'sign' => $friendInfo->getSign(),
            'groupid' => $groupId,
            'status' => FriendRelation::STATUS_TEXT[$friendInfo->getStatus()]
        ];
    }

    public function refuseApply(int $userApplicationId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_FRIEND);
        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_REFUSE);
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


}
