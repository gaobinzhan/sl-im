<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Model\Logic\GroupLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Validator\Annotation\Mapping\Validate;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use App\Validator\GroupValidator;
use Swoft\Validator\Annotation\Mapping\ValidateType;

/**
 * Class GroupController
 * @package App\Http\Controller
 * @Controller(prefix="group")
 */
class GroupController
{
    /**
     * @Inject()
     * @var GroupLogic
     */
    protected $groupLogic;

    /**
     * @RequestMapping(route="create",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="GroupValidator",fields={"group_name","avatar","size","introduction","validation"})
     */
    public function createGroup(Request $request)
    {
        try {
            $groupName = $request->parsedBody('group_name');
            $avatar = $request->parsedBody('avatar');
            $size = $request->parsedBody('size');
            $introduction = $request->parsedBody('introduction');
            $validation = $request->parsedBody('validation');
            $result = $this->groupLogic->createGroup($request->user, $groupName, $avatar, $size, $introduction, $validation);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getGroupRelation",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="GroupValidator",fields={"id"},type=ValidateType::GET)
     */
    public function getGroupRelation(Request $request)
    {
        try {
            $groupId = $request->get('id');
            $result = $this->groupLogic->getGroupRelationById(intval($groupId));
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getRecommendedGroup",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     */
    public function getRecommendedGroup()
    {
        try {
            $friends = $this->groupLogic->getRecommendedGroup(20);
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
    public function searchGroup(Request $request)
    {
        try {
            $keyword = $request->parsedBody('keyword');
            $page = $request->parsedBody('page');
            $size = $request->parsedBody('size');
            $friends = $this->groupLogic->searchGroup($keyword, $page, $size);
            return apiSuccess($friends);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="apply",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="GroupValidator",fields={"group_id","application_reason"})
     */
    public function apply(Request $request)
    {
        try {
            $userId = $request->user;
            $groupId = $request->parsedBody('group_id');
            $applicationReason = $request->parsedBody('application_reason');
            $result = $this->groupLogic->apply($userId, $groupId, $applicationReason);
            $msg = empty($result) ? '等待管理员验证 !' : '你已成功加入此群 !';
            return apiSuccess($result, 0, $msg);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="info",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="GroupValidator",fields={"group_id"},type=ValidateType::GET)
     */
    public function groupInfo(Request $request)
    {
        try {
            $groupId = $request->get('group_id');
            $groupInfo = $this->groupLogic->findGroupById(intval($groupId));
            return apiSuccess($groupInfo);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="agreeApply",method={RequestMethod::GET})
     * @Validate(validator="GroupValidator",fields={"user_application_id"},type=ValidateType::GET)
     * @Middleware(AuthMiddleware::class)
     */
    public function agreeApply(Request $request)
    {
        DB::beginTransaction();
        try {
            $userApplicationId = $request->get('user_application_id');
            $result = $this->groupLogic->agreeApply(intval($userApplicationId));
            DB::commit();
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }


    /**
     * @RequestMapping(route="refuseApply",method={RequestMethod::GET})
     * @Validate(validator="GroupValidator",fields={"user_application_id"},type=ValidateType::GET)
     * @Middleware(AuthMiddleware::class)
     */
    public function refuseApply(Request $request)
    {
        try {
            $userApplicationId = $request->get('user_application_id');
            $this->groupLogic->refuseApply(intval($userApplicationId));
            return apiSuccess($userApplicationId);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }
}
