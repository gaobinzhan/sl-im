<?php

namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Model\Dao\GroupDao;
use App\Model\Dao\GroupRelationDao;
use App\Model\Dao\UserApplicationDao;
use App\Model\Dao\UserDao;
use App\Model\Entity\Group;
use App\Model\Entity\GroupRelation;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class GroupLogic
 * @package App\Model\Logic
 * @Bean()
 */
class GroupLogic
{
    /**
     * @Inject()
     * @var GroupDao
     */
    protected $groupDao;


    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @Inject()
     * @var GroupRelationDao
     */
    protected $groupRelationDao;

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

    public function createGroup(int $userId, string $groupName, string $avatar, int $size, string $introduction, int $validation)
    {
        $groupId = $this->groupDao->create([
            'user_id' => $userId,
            'group_name' => $groupName,
            'avatar' => $avatar,
            'size' => $size,
            'introduction' => $introduction,
            'validation' => $validation
        ]);
        if (!$groupId) throw new \Exception('', ApiCode::GROUP_CREATE_FAIL);

        $groupRelationId = $this->createGroupRelation($userId, $groupId);
        if (!$groupRelationId) throw new \Exception('', ApiCode::GROUP_RELATION_CREATE_FAIL);

        $result = $this->findGroupById($groupId);

        return $result;
    }

    public function findGroupById(int $groupId)
    {
        $groupInfo = $this->groupDao->findGroupById($groupId);
        if (!$groupInfo) throw new \Exception('', ApiCode::GROUP_NOT_FOUND);
        return $groupInfo;
    }

    public function createGroupRelation(int $userId, int $groupId)
    {
        return $this->groupRelationDao->createGroupRelation([
            'user_id' => $userId,
            'group_id' => $groupId
        ]);
    }

    public function getGroupRelationById($groupId)
    {
        $this->findGroupById($groupId);

        $groupRelations = $this->groupRelationDao->findGroupRelationByGroupId($groupId);
        $userIds = array_column($groupRelations->toArray(), 'userId');

        $userInfos = $this->userDao->getUserByIds($userIds);
        $data = [];
        /** @var User $userInfo */
        foreach ($userInfos as $userInfo) {
            $data['list'][] = [
                'username' => $userInfo->getUsername(),
                'id' => $userInfo->getUserId(),
                'avatar' => $userInfo->getAvatar(),
                'sign' => $userInfo->getSign(),
            ];
        }
        return $data;
    }

    public function getGroup()
    {
        $request = context()->getRequest();

        $groupRelations = $this->groupRelationDao->getGroupRelationByUserId($request->user)->toArray();
        $groupIds = array_column($groupRelations, 'groupId');

        $groupInfos = $this->groupDao->getGroupByIds($groupIds);
        $result = [];
        /** @var Group $groupInfo */
        foreach ($groupInfos as $groupInfo) {
            $result[] = [
                'groupname' => $groupInfo->getGroupName(),
                'id' => $groupInfo->getGroupId(),
                'avatar' => $groupInfo->getAvatar()
            ];
        }
        return $result;
    }

    public function getRecommendedGroup(int $limit)
    {
        return $this->groupDao->getRecommendedGroup($limit);
    }

    public function searchGroup(string $keyword, int $page, int $size)
    {
        return $this->groupDao->searchGroup($keyword, $page, $size);
    }

    public function checkIsGroupRelation(int $userId, int $groupId)
    {
        $check = $this->groupRelationDao->checkIsGroupRelation($userId, $groupId);
        if ($check) throw new \Exception('', ApiCode::GROUP_RELATION_ALREADY);
        return $check;
    }

    public function checkGroupSize(int $groupId, int $size)
    {
        $count = $this->groupRelationDao->getGroupRelationCountByGroupId($groupId);
        if ($count >= $size) throw new \Exception('', ApiCode::GROUP_FULL);
        return $count;
    }

    public function apply(int $userId, int $groupId, string $applicationReason)
    {
        $this->checkIsGroupRelation($userId, $groupId);

        /** @var Group $groupInfo */
        $groupInfo = $this->findGroupById($groupId);

        $this->checkGroupSize($groupId, $groupInfo->getSize());


        $applicationStatus = ($groupInfo->getValidation() == Group::VALIDATION_NOT) ? UserApplication::APPLICATION_STATUS_ACCEPT : UserApplication::APPLICATION_STATUS_CREATE;

        $result = $this->userLogic->createUserApplication($userId, $groupInfo->getUserId(), $groupId, UserApplication::APPLICATION_TYPE_GROUP, $applicationReason, $applicationStatus, UserApplication::UN_READ);
        if (!$result) throw new \Exception('', ApiCode::USER_CREATE_APPLICATION_FAIL);

        if ($groupInfo->getValidation() == Group::VALIDATION_NOT) {
            $this->groupRelationDao->createGroupRelation([
                'user_id' => $userId,
                'group_id' => $groupId
            ]);
            return $groupInfo;
        }
        return '';
    }

    public function agreeApply(int $userApplicationId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_GROUP);

        $this->checkIsGroupRelation($userApplicationInfo->getUserId(), $userApplicationInfo->getGroupId());

        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_ACCEPT);

        /** @var Group $groupInfo */
        $groupInfo = $this->findGroupById($userApplicationInfo->getGroupId());

        $this->checkGroupSize($groupInfo->getGroupId(), $groupInfo->getSize());


        $result = $this->groupRelationDao->createGroupRelation([
            'user_id' => $userApplicationInfo->getUserId(),
            'group_id' => $groupInfo->getGroupId()
        ]);
        return $result;
    }

    public function refuseApply(int $userApplicationId)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->userLogic->beforeApply($userApplicationId, UserApplication::APPLICATION_TYPE_GROUP);
        $this->userApplicationDao->changeApplicationStatusById($userApplicationId, UserApplication::APPLICATION_STATUS_REFUSE);
        return $userApplicationInfo;
    }
}
