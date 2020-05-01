<?php declare(strict_types=1);


namespace App\Http\Controller\Api;

use App\Helper\AuthHelper;
use App\Helper\JwtHelper;
use App\Model\Entity\User;
use App\Model\Logic\FriendLogic;
use App\Model\Logic\GroupLogic;
use App\Model\Logic\UserLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Validator\Annotation\Mapping\Validate;
use App\Http\Middleware\AuthMiddleware;
use function view;

/**
 * Class UserController
 *
 * @Controller(prefix="api/user")
 * @package App\Http\Controller
 */
class UserController
{
    use AuthHelper;

    use JwtHelper;

    /**
     * @Inject()
     * @var UserLogic
     */
    protected $userLogic;

    /**
     * @Inject()
     * @var FriendLogic
     */
    protected $friendLogic;

    /**
     * @Inject()
     * @var GroupLogic
     */
    protected $groupLogic;

    /**
     * @RequestMapping(route="login",method={RequestMethod::POST})
     * @Validate(validator="UserValidator",fields={"email","password"})
     */
    public function login(Request $request, Response $response)
    {
        try {
            $email = $request->parsedBody('email');
            $password = $request->parsedBody('password');

            /** @var User $userInfo */
            $userInfo = $this->userLogic->login($email, $password);

            $token = $this->encrypt($userInfo['userId']);
            $userInfo['token'] = $token;
            return apiSuccess($userInfo);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="register",method={RequestMethod::POST})
     * @Validate(validator="UserValidator",fields={"username","email","password","code"})
     */
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $username = $request->parsedBody('username');
            $email = $request->parsedBody('email');
            $password = $request->parsedBody('password');
            $code = $request->parsedBody('code');
            $this->userLogic->register($username, $email, $password, $code);
            DB::commit();
            return apiSuccess();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="home",method={RequestMethod::GET})
     */
    public function home(Request $request, Response $response)
    {
        $menus = config('menu');
        return apiSuccess($menus);
    }


    /**
     * @RequestMapping(route="signOut",method={RequestMethod::GET})
     */
    public function signOut(Request $request, Response $response)
    {
        return context()->getResponse()->withCookie('IM_TOKEN', [
            'value' => '',
            'path' => '/'
        ])->redirect('/static/login');
    }

    /**
     * @RequestMapping(route="init",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     */
    public function userInit(Request $request)
    {
        try {
            $mine = $this->userLogic->getMine();
            $friend = $this->friendLogic->getFriend();
            $group = $this->groupLogic->getGroup();
            return apiSuccess(['mine' => $mine, 'friend' => $friend, 'group' => $group]);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getUnreadApplicationCount",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     */
    public function getUnreadApplicationCount(Request $request)
    {
        try {
            $count = $this->userLogic->getUnreadApplicationCount($request->user);
            return apiSuccess($count);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="getApplication",method={RequestMethod::POST})
     * @Validate(validator="SearchValidator",fields={"page","size"})
     * @Middleware(AuthMiddleware::class)
     */
    public function getApplication(Request $request)
    {
        try {
            $page = $request->parsedBody('page');
            $size = $request->parsedBody('size');
            $result = $this->userLogic->getApplication($request->user, $page, $size);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="info",method={RequestMethod::GET})
     * @Middleware(AuthMiddleware::class)
     */
    public function userInfo(Request $request)
    {
        try {
            return apiSuccess($request->userInfo);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="changeUserNameAndAvatar",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="UserValidator",fields={"username","avatar"})
     */
    public function changeUserNameAndAvatar(Request $request)
    {
        try {
            $username = $request->parsedBody('username');
            $avatar = $request->parsedBody('avatar');
            $result = $this->userLogic->changeUserNameAndAvatar($request->user, $username, $avatar);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }


    /**
     * @RequestMapping(route="setStatus",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="UserValidator",fields={"status"})
     */
    public function setStatus(Request $request)
    {
        try {
            $status = $request->parsedBody('status');
            $result = $this->userLogic->setUserStatus($request->user, $status);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="setSign",method={RequestMethod::POST})
     * @Middleware(AuthMiddleware::class)
     * @Validate(validator="UserValidator",fields={"sign"})
     */
    public function setSign(Request $request)
    {
        try {
            $sign = $request->parsedBody('sign');
            $result = $this->userLogic->setSign($request->user, $sign);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

}
