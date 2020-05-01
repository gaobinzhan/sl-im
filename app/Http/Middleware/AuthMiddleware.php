<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://swoft.org/docs
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Middleware;

use App\Exception\ApiException;
use App\ExceptionCode\ApiCode;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Server\Contract\MiddlewareInterface;

/**
 * Class AuthMiddleware - Custom middleware
 * @Bean()
 * @package App\Http\Middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ApiException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $authorization = $request->getHeaderLine('Authorization');

        $prefix = 'Bearer ';

        if (empty($authorization)) {
            $params = array_merge($request->getParsedBody(),$request->getQueryParams());
            $authorization = $prefix . ($params['access_token'] ?? '');
        }

        $publicKey = config('jwt.public_key');

        if (empty($publicKey)) {
            throw new ApiException('', ApiCode::JWT_PUBLIC_KEY_EMPTY);
        }

        if (empty($authorization) || !is_string($authorization) || strpos($authorization, $prefix) !== 0) {
            throw new ApiException('', ApiCode::AUTH_ERROR);
        }

        $jwt = substr($authorization, strlen($prefix));

        if (strlen(trim($jwt)) <= 0) {
            throw new ApiException('', ApiCode::AUTH_ERROR);
        }

        $payload = JWT::decode($jwt, $publicKey, [config('jwt.alg')]);


        if (isset($payload->user) && !is_numeric($payload->user)) {
            throw new ApiException('', ApiCode::AUTH_ERROR);
        }

        $request->user = $payload->user;

        $userInfo = bean('App\Model\Dao\UserDao')->findUserInfoById($request->user);

        if (empty($userInfo)) {
            throw new ApiException('', ApiCode::AUTH_ERROR);
        }

        $request->userInfo = $userInfo;


        return $handler->handle($request);
    }
}
