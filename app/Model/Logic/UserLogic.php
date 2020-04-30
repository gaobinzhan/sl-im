<?php

namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Helper\MemoryTable;
use App\Model\Dao\FriendRelationDao;
use App\Model\Dao\GroupDao;
use App\Model\Dao\UserApplicationDao;
use App\Model\Dao\UserDao;
use App\Model\Dao\UserLoginLogDao;
use App\Model\Entity\FriendRelation;
use App\Model\Entity\User;
use App\Model\Entity\UserApplication;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Task\Task;

/**
 * Class UserLogic
 * @package App\Model\Logic
 * @Bean()
 */
class UserLogic
{
    /**
     * @Inject()
     * @var UserDao
     */
    protected $userDao;

    /**
     * @Inject()
     * @var GroupDao
     */
    protected $groupDao;

    /**
     * @Inject()
     * @var UserLoginLogDao
     */
    protected $userLoginLogDao;

    /**
     * @Inject()
     * @var UserApplicationDao
     */
    protected $userApplicationDao;

    /**
     * @Inject()
     * @var FriendRelationDao
     */
    protected $friendRelationDao;


    public function findUserInfoById(int $userId)
    {
        $userInfo = $this->userDao->findUserInfoById($userId);

        if (!$userInfo) throw new \Exception('', ApiCode::USER_NOT_FOUND);

        return $userInfo;
    }

    public function register(string $username, string $email, string $password, string $code)
    {
        $userInfo = $this->findUserInfoByEmail($email);
        if ($userInfo) {
            throw new \Exception('', ApiCode::USER_EMAIL_ALREADY_USE);
        }

        \bean(VerifyLogic::class)->enterVerify($email, $code);

        return $this->createUser(
            [
                'email' => $email,
                'username' => $username,
                'password' => password_hash($password, CRYPT_BLOWFISH),
                'sign' => '',
                'status' => User::STATUS_OFFLINE,
                'avatar' => 'https://s.gravatar.com/avatar/' . md5(strtolower(trim($email))),
            ]
        );

    }

    public function login(string $email, string $password)
    {
        $userInfo = $this->findUserInfoByEmail($email);
        if (!$userInfo || $userInfo['deleted_at'] != null) {
            throw new \Exception('', ApiCode::USER_NOT_FOUND);
        }
        if (!password_verify($password, $userInfo['password'])) {
            throw new \Exception('', ApiCode::USER_PASSWORD_ERROR);
        }
        $this->createUserLoginLog($userInfo->getUserId());

        return $userInfo;
    }

    public function createUserLoginLog(int $userId)
    {
        $request = context()->getRequest();
        $ip = empty($request->getHeaderLine('x-real-ip')) ? $request->getServerParams()['remote_addr'] : $request->getHeaderLine('x-real-ip');
        $data = [
            'user_id' => $userId,
            'user_login_ip' => $ip
        ];
        return $this->userLoginLogDao->createUserLoginLog($data);
    }

    public function findUserInfoByEmail(string $email)
    {
        return $this->userDao->findUserInfoByEmail($email);
    }

    public function createUser(array $data)
    {
        return $this->userDao->createUser($data);
    }

    public function getMine()
    {
        /** @var User $userInfo */
        $userInfo = context()->getRequest()->userInfo;
        return [
            'username' => $userInfo->getUsername(),
            'id' => $userInfo->getUserId(),
            'status' => User::STATUS_TEXT[User::STATUS_ONLINE],
            'sign' => $userInfo->getSign(),
            'avatar' => $userInfo->getAvatar(),
        ];
    }

    public function createUserApplication(
        int $userId,
        int $receiverId,
        int $groupId,
        string $applicationType,
        string $applicationReason,
        int $applicationStatus = UserApplication::APPLICATION_STATUS_CREATE,
        int $readState = UserApplication::UN_READ)
    {
        return $this->userApplicationDao->createUserApplication([
            'user_id' => $userId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'application_type' => $applicationType,
            'application_status' => $applicationStatus,
            'application_reason' => $applicationReason,
            'read_state' => $readState
        ]);
    }

    public function getUnreadApplicationCount(int $userId)
    {
        return $this->userApplicationDao->getUnreadApplicationCount($userId);
    }

    public function getApplication(int $userId, int $page, int $size)
    {
        $applications = $this->userApplicationDao->getApplication($userId, $page, $size);
        $result = [];
        $userIds = [];
        $groupIds = [];
        $applicationIds = [];
        /** @var UserApplication $application */
        foreach ($applications['list'] as $application) {
            ($userId != $application['userId']) && array_push($applicationIds, $application['userApplicationId']);
            $applicationRole = ($userId == $application['userId'])
                ? (($application['applicationStatus'] != UserApplication::APPLICATION_STATUS_CREATE)
                    ? $applicationRole = UserApplication::APPLICATION_SYSTEM
                    : UserApplication::APPLICATION_CREATE_USER)
                : UserApplication::APPLICATION_RECEIVER_USER;

            array_push($userIds, $application['userId']);
            array_push($userIds, $application['receiverId']);

            ($application['applicationType'] == UserApplication::APPLICATION_TYPE_GROUP) && array_push($groupIds, $application['groupId']);

            $result[] = [
                'user_application_id' => $application['userApplicationId'],
                'user_id' => $application['userId'],
                'receiver_id' => $application['receiverId'],
                'group_id' => $application['groupId'],
                'application_role' => $applicationRole,
                'application_type' => $application['applicationType'],
                'created_at' => $application['createdAt'],
                'updated_at' => $application['updatedAt'],
                'application_status' => $application['applicationStatus'],
                'application_status_text' => UserApplication::APPLICATION_STATUS_TEXT[$application['applicationStatus']],
                'application_reason' => $application['applicationReason']
            ];
        }
        $userInfos = array_column($this->userDao->getUserByIds($userIds)->toArray(), null, 'userId');
        $groupInfos = array_column($this->groupDao->getGroupByIds($groupIds)->toArray(), null, 'groupId');

        foreach ($result as &$item) {
            if ($item['application_type'] == UserApplication::APPLICATION_TYPE_GROUP) {
                $item['group_name'] = $groupInfos[$item['group_id']]['groupName'] ?? '';
                $item['group_avatar'] = $groupInfos[$item['group_id']]['avatar'] ?? '';
            }
            $item['user_name'] = $userInfos[$item['user_id']]['username'] ?? '';
            $item['user_avatar'] = $userInfos[$item['user_id']]['avatar'] ?? '';
            $item['receiver_name'] = $userInfos[$item['receiver_id']]['username'] ?? '';
            $item['receiver_avatar'] = $userInfos[$item['receiver_id']]['avatar'] ?? '';
        }
        $applications['list'] = $result;
        if (!empty($applicationIds)) {
            $change = $this->userApplicationDao->changeApplicationReadStateByIdsAndReceiverId($applicationIds, $userId, UserApplication::ALREADY_READ);
            if (!$change) throw new \Exception('', ApiCode::USER_APPLICATION_SET_READ_FAIL);
        }
        return $applications;
    }

    public function changeUserNameAndAvatar(int $userId, string $username, string $avatar)
    {
        return $this->changeUserInfoById($userId, [
            'username' => $username,
            'avatar' => $avatar
        ]);
    }

    public function changeUserInfoById(int $userId, array $data)
    {
        $change = $this->userDao->changeUserInfoById($userId, $data);
        if (!$change) throw new \Exception('', ApiCode::USER_INFO_MODIFY_FAIL);
        return $change;
    }

    public function findUserApplicationById(int $id)
    {
        $userApplication = $this->userApplicationDao->findUserApplicationById($id);
        if (!$userApplication) throw new \Exception('', ApiCode::USER_APPLICATION_NOT_FOUND);
        return $userApplication;
    }

    public function checkApplicationProcessed(UserApplication $userApplication)
    {
        if ($userApplication->getApplicationStatus() != UserApplication::APPLICATION_STATUS_CREATE) {
            throw new \Exception('', ApiCode::USER_APPLICATION_PROCESSED);
        }

        if ($userApplication->getReceiverId() != context()->getRequest()->user) {
            throw new \Exception('', ApiCode::NO_PERMISSION_PROCESS);
        }
    }

    public function beforeApply(int $userApplicationId, string $userApplicationType)
    {
        /** @var UserApplication $userApplicationInfo */
        $userApplicationInfo = $this->findUserApplicationById($userApplicationId);
        $this->checkApplicationProcessed($userApplicationInfo);
        if ($userApplicationInfo->getApplicationType() != $userApplicationType) {
            throw new \Exception('', ApiCode::USER_APPLICATION_TYPE_WRONG);
        }
        return $userApplicationInfo;
    }

    public function setUserStatus(int $userId, int $status = User::STATUS_ONLINE)
    {
        $this->userDao->changeUserInfoById($userId, [
            'status' => $status
        ]);
        $friendIds = $this->friendRelationDao->getFriendIdsByUserId($userId)->toArray();
        $friendIds = array_column($friendIds, 'friendId');
        /** @var MemoryTable $MemoryTable */
        $MemoryTable = bean('App\Helper\MemoryTable');

        $onlineFds = [];
        foreach ($friendIds as $friendId) {
            $fd = $MemoryTable->get(MemoryTable::USER_TO_FD, $friendId, 'fd');
            $fd && array_push($onlineFds, $fd);
        }

        $result = [
            'user_id' => $userId,
            'status' => FriendRelation::STATUS_TEXT[$status]
        ];
        Task::co('User', 'setUserStatus', [$onlineFds, $result]);

        return $result;

    }

    public function setSign(int $userId, string $sign)
    {
        return $this->changeUserInfoById($userId, [
            'sign' => $sign
        ]);
    }
}
