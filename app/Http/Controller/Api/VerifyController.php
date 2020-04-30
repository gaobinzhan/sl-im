<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Http\Controller\Api;

use App\Model\Logic\VerifyLogic;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Validator\Annotation\Mapping\Validate;

/**
 * Class VerifyController
 * @package App\Http\Controller\Api
 * @Controller(prefix="api/verify")
 */
class VerifyController
{
    /**
     * @Inject()
     * @var VerifyLogic
     */
    protected $verifyLogic;

    /**
     * @RequestMapping(route="sendMail",method={RequestMethod::POST})
     * @Validate(validator="UserValidator",fields={"email"})
     */
    public function sendMail(Request $request)
    {
        try {
            $email = $request->parsedBody('email');
            $result = $this->verifyLogic->sendMail($email);
            return apiSuccess($result);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }
}
