<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://swoft.org/docs
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use App\Model\Logic\FriendLogic;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Validator\Annotation\Mapping\Validate;
use Swoft\Validator\Annotation\Mapping\ValidateType;

/**
 * Class FriendController
 *
 * @Controller(prefix="friend")
 * @package App\Http\Controller
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
     * @RequestMapping(route="createFriendGroup",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="FriendValidator",fields={"friend_group_name"})
     */
    public function createFriendGroup(Request $request)
    {
        try {
            $friendGroupName = $request->parsedBody('friend_group_name');

            $result = $this->friendLogic->createFriendGroup($request->user, $friendGroupName);


            return apiSuccess([
                'id' => $result->getFriendGroupId(),
                'groupname' => $result->getFriendGroupName()
            ]);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getRecommendedFriend",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     */
    public function getRecommendedFriend()
    {
        try {
            $friends = $this->friendLogic->getRecommendedFriend(20);
            return apiSuccess($friends);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="search",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="SearchValidator",fields={"keyword","page","size"})
     */
    public function searchFriend(Request $request)
    {
        try {
            $keyword = $request->parsedBody('keyword');
            $page = $request->parsedBody('page');
            $size = $request->parsedBody('size');
            $friends = $this->friendLogic->searchFriend($keyword, $page, $size);
            return apiSuccess($friends);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="apply",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="FriendValidator",fields={"receiver_id","friend_group_id","application_reason"})
     */
    public function apply(Request $request)
    {
        try {
            $userId = $request->user;
            $receiverId = $request->parsedBody('receiver_id');
            $friendGroupId = $request->parsedBody('friend_group_id');
            $applicationReason = $request->parsedBody('application_reason');
            $this->friendLogic->apply($userId, $receiverId, $friendGroupId, $applicationReason);
            return apiSuccess();
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="info",method={RequestMethod::GET})
     * @Validate(validator="UserValidator",fields={"user_id"},type=ValidateType::GET)
     * @Middleware(AuthMiddleware::class)
     */
    public function friendInfo(Request $request)
    {
        try {
            $userId = $request->get('user_id');
            $userInfo = $this->userLogic->findUserInfoById(intval($userId));
            return apiSuccess($userInfo);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="agreeApply",method={RequestMethod::GET})
     * @Validate(validator="FriendValidator",fields={"user_application_id","friend_group_id"},type=ValidateType::GET)
     * @Middleware(AuthMiddleware::class)
     */
    public function agreeApply(Request $request)
    {
        DB::beginTransaction();
        try {
            $userApplicationId = $request->get('user_application_id');
            $friendGroupId = $request->get('friend_group_id');
            $result = $this->friendLogic->agreeApply(intval($userApplicationId), intval($friendGroupId));
            DB::commit();
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }


    /**
     * @RequestMapping(route="refuseApply",method={RequestMethod::GET})
     * @Validate(validator="FriendValidator",fields={"user_application_id"},type=ValidateType::GET)
     * @Middleware(AuthMiddleware::class)
     */
    public function refuseApply(Request $request)
    {
        try {
            $userApplicationId = $request->get('user_application_id');
            $this->friendLogic->refuseApply(intval($userApplicationId));
            return apiSuccess($userApplicationId);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getChatHistory",method={RequestMethod::POST})
     * @Validate(validator="FriendValidator",fields={"from_user_id"})
     * @Validate(validator="SearchValidator",fields={"page","size"})
     * @Middleware(AuthMiddleware::class)
     */
    public function getChatHistory(Request $request)
    {
        try {
            $fromUserId = $request->parsedBody('from_user_id');
            $page = $request->parsedBody('page');
            $size = $request->parsedBody('size');
            $result = $this->friendLogic->getChatHistory($fromUserId, $request->user, $page, $size);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }
}
