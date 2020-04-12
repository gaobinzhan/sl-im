<?php

use App\ExceptionCode\ApiCode;
use App\Helper\JwtHelper;

/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

function user_func(): string
{
    return 'hello';
}

if (!function_exists('apiSuccess')) {

    /**
     * @param $data
     * @param int $code
     * @param string $msg
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response|\Swoft\Task\Response
     */
    function apiSuccess($data = [], $code = 0, $msg = 'Success')
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        return context()->getResponse()->withStatus(200)->withData($result);
    }
}


if (!function_exists('apiError')) {


    /**
     * @param $code
     * @param string $msg
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response|\Swoft\Task\Response
     */
    function apiError($code = -1, $msg = 'Error')
    {
        $code = ($code == 0) ? -1 : $code;
        $msg = ApiCode::$errorMessages[$code] ?? $msg;
        $result = [
            'code' => $code,
            'msg' => $msg,
        ];
        return context()->getResponse()->withStatus(200)->withData($result);
    }
}


if (!function_exists('throwApiException')) {

    /**
     * @param $code
     * @param string $msg
     * @param string $file
     * @param string $trace
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response|\Swoft\Task\Response
     */
    function throwApiException($code, $msg = 'Error', $file = '', $trace = '')
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
        ];
        if (APP_DEBUG) {
            $result = array_merge($result, [
                'file' => $file,
                'trace' => $trace
            ]);
        }
        return context()->getResponse()->withStatus(200)->withData($result);
    }
}

if (!function_exists('checkAuth')) {
    function checkAuth()
    {
        $request = context()->getRequest();
        $token = $request->getCookieParams()['IM_TOKEN'] ?? '';
        if (!$token || !is_string($token) || !$userId = JwtHelper::decrypt($token)) {
            return false;
        }
        $userInfo = bean('App\Model\Dao\UserDao')->findUserInfoById($userId);
        if (!$userInfo) {
            return false;
        }
        $request->user = $userId;
        $request->userInfo = $userInfo;

        return $userId;
    }
}
if (!function_exists('wsSuccess')) {
    function wsSuccess($cmd = \App\Common\WsMessage::WS_MESSAGE_CMD_EVENT, $method = '', $data = [], $msg = 'Success')
    {
        $result = [
            'cmd' => $cmd,
            'method' => $method,
            'msg' => $msg,
            'data' => $data
        ];

        return json_encode($result);
    }
}

if (!function_exists('wsError')) {
    function wsError($msg = 'Error', $cmd = \App\Common\WsMessage::WS_MESSAGE_CMD_ERROR, $data = [])
    {
        $result = [
            'cmd' => $cmd,
            'msg' => $msg,
            'data' => $data
        ];
        return json_encode($result);
    }
}
